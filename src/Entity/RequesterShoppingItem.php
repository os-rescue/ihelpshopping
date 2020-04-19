<?php

namespace IHelpShopping\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Gedmo\Blameable\Traits\Blameable;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\Timestampable;
use IHelpShopping\Entity\User;
use IHelpShopping\Annotation\UserAware;
use IHelpShopping\Validator\Constraints as IHelpShoppingAssert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Uuid;

/**
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={
 *              "groups"={
 *                  "requester_shopping_item_normalized",
 *                  "requester_helpers",
 *                  "helper_requesters",
 *              },
 *              "enable_max_depth"=true
 *          },
 *          "denormalization_context"={
 *              "groups"={"requester_shopping_item_model"},
 *              "allow_extra_attributes"=false,
 *          },
 *     },
 *     collectionOperations={
 *          "post",
 *      },
 *     itemOperations={
 *         "get",
 *         "put",
 *         "delete"
 *     },
 *     subresourceOperations={
 *         "api_users_shopping_items_get_subresource"= {
 *             "normalization_context"={"groups"={"requester_shopping_item_normalized"}},
 *         }
 *     },
 * )
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={"name": {"default_direction": "ASC"}},
 *     arguments={"orderParameterName"="order"}
 * )
 * @ORM\Table(
 *     name="ihs_requester_shopping_item",
 *     indexes={
 *          @Index(name="name_idx", columns={"name"}),
 *     },
 * )
 * @ORM\Entity()
 *
 * @final
 */
class RequesterShoppingItem
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETE = 'complete';

    use Blameable;
    use Timestampable;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max = 255, maxMessage = "length.max,{{ limit }}")
     * @Assert\NotNull(message="not_null")
     * @Assert\NotBlank(message="not_blank")
     * @IHelpShoppingAssert\Whitespace()
     * @Groups({
     *     "requester_shopping_item_model",
     *     "requester_shopping_item_normalized",
     *     "helper_requesters",
     *     "requester_helpers"
     * })
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotNull(message="not_null")
     * @Assert\NotBlank(message="not_blank")
     * @Assert\Choice(
     *     {RequesterShoppingItem::STATUS_PENDING, RequesterShoppingItem::STATUS_COMPLETE},
     *     message="invalid"
     * )
     * @Groups({
     *     "requester_shopping_item_model",
     *     "requester_shopping_item_normalized",
     *     "helper_requesters",
     *     "requester_helpers"
     * })
     */
    protected $status;

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

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="IHelpShopping\Entity\User", inversedBy="shoppingItems")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * @MaxDepth(1)
     */
    private $createdBy;

    public function __construct()
    {
        $this->status = self::STATUS_PENDING;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
