<?php

namespace IHelpShopping\Entity;

use API\UserBundle\Model\User as BaseUser;
use API\UserBundle\Model\UserInterface;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
 *                  "requester_helpers",
 *                  "helper_requesters",
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
 *         "put",
 *     }
 * )
 * @ApiFilter(SearchFilter::class, properties={"accountType": "exact"})
 * @ApiFilter(RangeFilter::class, properties={"nbPendingItems"})
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={"firstName", "lastName"},
 *     arguments={"orderParameterName"="order"},
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
    public const ACCOUNT_TYPE_HELPER = 'helper';
    public const ACCOUNT_TYPE_REQUESTER = 'requester';

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
     *     "requester_helpers",
     *     "helper_requesters",
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
     *     "requester_helpers",
     *     "helper_requesters",
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
     *     "requester_helpers",
     *     "helper_requesters",
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
     *     "requester_helpers",
     *     "helper_requesters",
     * })
     */
    protected $address;

    /**
     * @ORM\Column(name="title", type="string", length=10, nullable=true)
     * @Groups({
     *     "user_model",
     *     "item_user_normalized",
     *     "requester_helpers",
     *     "helper_requesters",
     * })
     */
    protected $title;

    /**
     * @ORM\Column(name="phone_number", type="string", length=35, nullable=true)
     * @IHelpShoppingAssert\ValidPhone()
     * @Groups({
     *     "user_model",
     *     "item_user_normalized",
     *     "requester_helpers",
     *     "helper_requesters",
     * })
     */
    protected $phoneNumber;

    /**
     * @ORM\Column(name="mobile_number", type="string", length=35, nullable=true)
     * @IHelpShoppingAssert\ValidPhone()
     * @Groups({
     *     "user_model",
     *     "item_user_normalized",
     *     "requester_helpers",
     *     "helper_requesters",
     * })
     */
    protected $mobileNumber;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Assert\Choice({User::ACCOUNT_TYPE_HELPER, User::ACCOUNT_TYPE_REQUESTER}, message="invalid")
     * @Groups({"user_model", "item_user_normalized", "collection_users_normalized"})
     */
    protected $accountType;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     * @Groups({
     *     "item_user_normalized",
     *     "collection_users_normalized",
     *     "requester_helpers",
     *     "helper_requesters",
     * })
     */
    protected $nbPendingItems = 0;

    /**
     * @ORM\OneToMany(
     *     targetEntity="IHelpShopping\Entity\RequesterShoppingItem",
     *     mappedBy="createdBy",
     * )
     * @ApiProperty(attributes={"fetchEager": false})
     * @ApiSubresource(maxDepth=1)
     */
    protected $shoppingItems;

    /**
     * @ORM\OneToMany(
     *     targetEntity="IHelpShopping\Entity\HelperRequester",
     *     mappedBy="requester"
     * )
     * @ApiProperty(attributes={"fetchEager": false})
     * @ApiSubresource(maxDepth=1)
     */
    protected $helpers;

    /**
     * @ORM\OneToMany(
     *     targetEntity="IHelpShopping\Entity\HelperRequester",
     *     mappedBy="helper",
     *     cascade={"persist"}
     * )
     * @Groups({"user_model"})
     * @ApiProperty(attributes={"fetchEager": false})
     * @ApiSubresource(maxDepth=1)
     */
    protected $requesters;
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
        $this->helpers = new ArrayCollection();
        $this->requesters = new ArrayCollection();
        $this->shoppingItems = new ArrayCollection();
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

    public function getAccountType(): ?string
    {
        return $this->accountType;
    }

    public function setAccountType(?string $accountType): self
    {
        $this->accountType = $accountType;

        return $this;
    }

    public function getNbPendingItems(): int
    {
        return $this->nbPendingItems;
    }

    public function setNbPendingItems(int $nbPendingItems): self
    {
        $this->nbPendingItems = $nbPendingItems;

        return $this;
    }

    /**
     * @return Collection|RequesterShoppingItem[]
     */
    public function getShoppingItems(): Collection
    {
        return $this->shoppingItems;
    }

    /**
     * @ApiProperty(iri="http://schema.org/admin")
     * @Groups({"item_user_normalized", "collection_users_normalized"})
     */
    public function isAdmin(): bool
    {
        return \in_array(self::ROLE_SUPER_ADMIN, $this->roles, true);
    }

    /**
     * @return Collection|HelperRequester[]
     */
    public function getHelpers(): Collection
    {
        return $this->helpers;
    }

    public function addHelper(HelperRequester $helper): self
    {
        if (!$this->helpers->contains($helper)) {
            $this->helpers->add($helper);
            $helper->setRequester($this);
        }

        return $this;
    }

    public function removeHelper(HelperRequester $helper): self
    {
        if ($this->helpers->contains($helper)) {
            $this->helpers->removeElement($helper);
            if ($helper->getRequester() === $this) {
                $helper->setRequester(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|HelperRequester[]
     */
    public function getRequesters(): Collection
    {
        return $this->requesters;
    }

    public function addRequester(HelperRequester $requester): self
    {
        if (!$this->requesters->contains($requester)) {
            $this->requesters->add($requester);
            $requester->setHelper($this);
        }

        return $this;
    }

    public function removeRequester(HelperRequester $requester): self
    {
        if ($this->requesters->contains($requester)) {
            $this->requesters->removeElement($requester);
            if ($requester->getHelper() === $this) {
                $requester->setHelper(null);
            }
        }

        return $this;
    }
}
