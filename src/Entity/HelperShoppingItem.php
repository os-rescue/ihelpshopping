<?php

namespace IHelpShopping\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Uuid;

/**
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={
 *              "groups"={
 *                  "requester_helpers",
 *                  "helper_requesters",
 *              },
 *              "enable_max_depth"=true
 *          },
 *          "denormalization_context"={
 *              "groups"={"helper_shopping_item_model"},
 *              "allow_extra_attributes"=false,
 *          },
 *     },
 *     collectionOperations={
 *         "post",
 *     },
 *     itemOperations={
 *         "get",
 *     }
 * )
 * @ORM\Table(
 *     name="ihs_helper_shopping_item",
 * )
 * @ORM\Entity()
 *
 * @final
 */
class HelperShoppingItem
{
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_DONE = 'done';

    use Timestampable;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="IHelpShopping\Entity\HelperRequester", inversedBy="helperShoppingItems")
     * @ORM\JoinColumn(name="helper_requester_id", referencedColumnName="id", nullable=false)
     * @Groups({"user_model"})
     * @MaxDepth(1)
     */
    protected $helperRequester;

    /**
     * @ORM\ManyToOne(targetEntity="IHelpShopping\Entity\RequesterShoppingItem")
     * @ORM\JoinColumn(name="requester_shopping_item_id", referencedColumnName="id", nullable=false)
     * @Groups({
     *     "helper_shopping_item_model",
     *     "user_model",
     *     "helper_requesters",
     *     "requester_helpers"
     * })
     * @MaxDepth(1)
     */
    protected $requesterShoppingItem;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull(message="not_null")
     * @Assert\NotBlank(message="not_blank")
     * @Assert\Choice(
     *     {HelperShoppingItem::STATUS_CONFIRMED, HelperShoppingItem::STATUS_DONE},
     *     message="invalid"
     * )
     * @Groups({
     *     "helper_shopping_item_model",
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

    public function __construct()
    {
        $this->status = self::STATUS_CONFIRMED;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getHelperRequester(): ?HelperRequester
    {
        return $this->helperRequester;
    }

    public function setHelperRequester(?HelperRequester $helperRequester): self
    {
        $this->helperRequester = $helperRequester;

        return $this;
    }

    public function getRequesterShoppingItem(): ?RequesterShoppingItem
    {
        return $this->requesterShoppingItem;
    }

    public function setRequesterShoppingItem(?RequesterShoppingItem $requesterShoppingItem): self
    {
        $this->requesterShoppingItem = $requesterShoppingItem;

        return $this;
    }
}
