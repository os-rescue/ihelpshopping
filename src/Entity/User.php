<?php

namespace IHelpShopping\Entity;

use API\UserBundle\Model\User as BaseUser;
use API\UserBundle\Model\UserInterface;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use IHelpShopping\Traits\UserNameTrait;
use IHelpShopping\Validator\Constraints as IHelpShoppingAssert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Uuid;

/**
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={
 *              "groups"={
 *                  "item_user_normalized",
 *                  "collection_users_normalized",
 *              },
 *              "enable_max_depth"=true
 *          },
 *          "denormalization_context"={
 *              "groups"={"user_model"},
 *              "allow_extra_attributes"=false,
 *              "datetime_format"="Y-m-d\TH:i:sZ",
 *          },
 *     },
 *     collectionOperations={
 *          "get"={
 *              "pagination_client_items_per_page"=true,
 *              "normalization_context"={"groups"={"collection_users_normalized"}}
 *          },
 *          "post",
 *      },
 *     itemOperations={
 *         "get"={"normalization_context"={"groups"={"item_user_normalized"}}},
 *     }
 * )
 * @ORM\Table(
 *     name="ihs_user",
 *     indexes={
 *          @Index(name="first_name_idx", columns={"first_name"}),
 *          @Index(name="last_name_idx", columns={"last_name"}),
 *          @Index(name="middle_name_idx", columns={"middle_name"}),
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(
 *             name="unique_user_idx",
 *             columns={"email_canonical"}
 *         )
 *     },
 * )
 * @ORM\Entity()
 * @ORM\EntityListeners({
 *     "IHelpShopping\EventListener\User\UserEmailListener",
 * })
 * @UniqueEntity(
 *     fields={"emailCanonical"},
 *     ignoreNull=false,
 *     message="already.exist",
 *     groups={"Default"}
 * )
 * @IHelpShoppingAssert\CurrentPassword(groups={"SettingPassword"})
 *
 * @final
 */
class User extends BaseUser
{
    use Timestampable;
    use UserNameTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    protected $id;

    /**
     * @ORM\Column(name="email", type="string", length=180)
     * @Groups({
     *     "item_user_normalized",
     *     "user_model",
     * })
     */
    protected $email;

    /**
     * @ORM\Column(name="first_name", type="string", length=100)
     * @Assert\Length(max = 100, maxMessage = "length.max,{{ limit }}")
     * @Assert\NotNull(message="not_null")
     * @Assert\NotBlank(message="not_blank")
     * @IHelpShoppingAssert\Whitespace()
     * @Groups({
     *     "user_model",
     *     "item_user_normalized",
     *     "collection_users_normalized",
     * })
     */
    protected $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length=100)
     * @Assert\Length(max = 100, maxMessage = "length.max,{{ limit }}")
     * @Assert\NotNull(message="not_null")
     * @Assert\NotBlank(message="not_blank")
     * @IHelpShoppingAssert\Whitespace()
     * @Groups({
     *     "user_model",
     *     "item_user_normalized",
     *     "collection_users_normalized",
     * })
     */
    protected $lastName;

    /**
     * @ORM\Column(name="middle_name", type="string", length=100, nullable=true)
     * @Assert\Length(max = 100, maxMessage = "length.max,{{ limit }}")
     * @Groups({
     *     "user_model",
     *     "item_user_normalized",
     *     "collection_users_normalized",
     * })
     */
    protected $middleName;

    /**
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *      minMessage = "length.min,{{ limit }}",
     *      maxMessage = "length.max,{{ limit }}",
     *     groups={"Default"}
     * )
     * @IHelpShoppingAssert\Whitespace()
     * @Groups({
     *     "user_model",
     *     "item_user_normalized",
     * })
     */
    protected $address;

    /**
     * @ORM\Column(name="title", type="string", length=10, nullable=true)
     * @Groups({
     *     "user_model",
     *     "item_user_normalized"
     * })
     */
    protected $title;

    /**
     * @ORM\Column(name="phone_number", type="string", length=35, nullable=true)
     * @IHelpShoppingAssert\ValidPhone()
     * @Groups({"user_model", "item_user_normalized"})
     */
    protected $phoneNumber;

    /**
     * @ORM\Column(name="mobile_number", type="string", length=35, nullable=true)
     * @IHelpShoppingAssert\ValidPhone()
     * @Groups({"user_model", "item_user_normalized"})
     */
    protected $mobileNumber;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    public function __construct()
    {
        parent::__construct();

        $this->roles[] = UserInterface::ROLE_DEFAULT;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function addRole(string $role): UserInterface
    {
        $role = strtoupper($role);
        if (static::ROLE_DEFAULT === $role) {
            return $this;
        }

        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
        $this->roles = array_values($this->roles);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = null !== $title ? strtolower($title) : null;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = str_replace(' ', '', $phoneNumber);

        return $this;
    }

    public function getMobileNumber(): ?string
    {
        return $this->mobileNumber;
    }

    public function setMobileNumber(?string $mobileNumber): self
    {
        $this->mobileNumber = str_replace(' ', '', $mobileNumber);

        return $this;
    }

    /**
     * @ApiProperty(iri="http://schema.org/admin")
     * @Groups({"item_user_normalized", "collection_users_normalized"})
     */
    public function isAdmin(): bool
    {
        return \in_array(self::ROLE_SUPER_ADMIN, $this->roles, true);
    }
}
