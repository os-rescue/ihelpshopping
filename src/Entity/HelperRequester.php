<?php

namespace IHelpShopping\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\Timestampable;
use IHelpShopping\Entity\User;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Uuid;

/**
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={
 *              "groups"={"helper_requester_normalized"},
 *              "enable_max_depth"=true
 *          },
 *          "denormalization_context"={
 *              "groups"={"helper_requester_model"},
 *              "allow_extra_attributes"=false,
 *          },
 *     },
 *     collectionOperations={
 *         "post",
 *     },
 *     itemOperations={
 *         "get",
 *     },
 *     subresourceOperations={
 *         "api_users_helpers_get_subresource"= {
 *             "normalization_context"={"groups"={"requester_helpers"}},
 *         }
 *     },
 *     subresourceOperations={
 *         "api_users_requesters_get_subresource"= {
 *             "normalization_context"={"groups"={"helper_requesters"}},
 *         }
 *     }
 * )
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={"requester.firstName": "ASC", "helper.firstName": "ASC"},
 *     arguments={"orderParameterName"="order"},
 * )
 * @ORM\Table(
 *     name="ihs_helper_requester",
 * )
 * @ORM\Entity()
 *
 * @final
 */
class HelperRequester
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';

    use Timestampable;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="IHelpShopping\Entity\User", inversedBy="requesters")
     * @ORM\JoinColumn(name="helper_id", referencedColumnName="id", nullable=false)
     * @Groups({"user_model", "helper_requester_normalized", "requester_helpers"})
     * @MaxDepth(1)
     */
    protected $helper;

    /**
     * @ORM\ManyToOne(targetEntity="IHelpShopping\Entity\User", inversedBy="helpers")
     * @ORM\JoinColumn(name="requester_id", referencedColumnName="id", nullable=false)
     * @Groups({"user_model", "helper_requester_normalized", "helper_requesters"})
     * @MaxDepth(1)
     */
    protected $requester;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotNull(message="not_null")
     * @Assert\NotBlank(message="not_blank")
     * @Assert\Choice(
     *     {HelperRequester::STATUS_PENDING, HelperRequester::STATUS_CONFIRMED},
     *     message="invalid"
     * )
     * @Groups({
     *     "helper_requester_model",
     *     "helper_requester_normalized",
     * })
     */
    protected $status;

    /**
     * @ORM\OneToMany(
     *     targetEntity="IHelpShopping\Entity\HelperShoppingItem",
     *     mappedBy="helperRequester",
     *     cascade={"persist"}
     * )
     * @Groups({"user_model"})
     * @ApiProperty(attributes={"fetchEager": false})
     */
    protected $helperShoppingItems;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    public function __construct()
    {
        $this->status = self::STATUS_PENDING;
        $this->helperShoppingItems = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getHelper(): User
    {
        return $this->helper;
    }

    public function setHelper(?User $helper): self
    {
        $this->helper = $helper;

        return $this;
    }

    public function getRequester(): User
    {
        return $this->requester;
    }

    public function setRequester(?User $requester): self
    {
        $this->requester = $requester;

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

    /**
     * @return Collection|HelperShoppingItem[]
     */
    public function getHelperShoppingItems(): Collection
    {
        return $this->helperShoppingItems;
    }

    public function addHelperShoppingItem(HelperShoppingItem $helperShoppingItem): self
    {
        if (!$this->helperShoppingItems->contains($helperShoppingItem)) {
            $this->helperShoppingItems->add($helperShoppingItem);
            $helperShoppingItem->setHelperRequester($this);
        }

        return $this;
    }

    public function removeHelperShoppingItem(HelperShoppingItem $helperShoppingItem): self
    {
        if ($this->helperShoppingItems->contains($helperShoppingItem)) {
            $this->helperShoppingItems->removeElement($helperShoppingItem);
            if ($helperShoppingItem->getHelperRequester() === $this) {
                $helperShoppingItem->setHelperRequester(null);
            }
        }

        return $this;
    }
}
