<?php
/*
 *
 * This file is part of the Kiwicore package.
 *
 * (c) Simcao EI <dev@simcao.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *  2023
 */

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le nom du produit est trop court',
        maxMessage: 'Le nom du produit est trop long. Limite : {{ limit }} characters',
    )]
    #[Assert\Type(
        type: 'string',
        message: 'Le format n\'est pas celui attendu'
    )]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\Length(
        min: 2,
        max: 400,
        minMessage: 'Le nom du produit est trop court',
        maxMessage: 'Le nom du produit est trop long. Limite : {{ limit }} characters',
    )]
    #[Assert\Type(
        type: 'string',
        message: 'Le format n\'est pas celui attendu'
    )]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[Assert\NotBlank]
    #[Assert\Type(
        type: 'integer',
        message: 'Le format n\'est pas celui attendu'
    )]
    #[Assert\Positive(message: 'Le prix ne peut pas être négatif')]
    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reference = null;

    #[ORM\Column]
    private ?bool $stockable = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductImage::class, orphanRemoval: true)]
    private Collection $productImages;

    public function __construct()
    {
        $this->productImages = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function isStockable(): ?bool
    {
        return $this->stockable;
    }

    public function setStockable(bool $stockable): self
    {
        $this->stockable = $stockable;

        return $this;
    }

    /**
     * @return Collection<int, ProductImage>
     */
    public function getProductImages(): Collection
    {
        return $this->productImages;
    }

    public function addProductImage(ProductImage $productImage): self
    {
        if (!$this->productImages->contains($productImage)) {
            $this->productImages->add($productImage);
            $productImage->setProduct($this);
        }

        return $this;
    }

    public function removeProductImage(ProductImage $productImage): self
    {
        if ($this->productImages->removeElement($productImage)) {
            // set the owning side to null (unless already changed)
            if ($productImage->getProduct() === $this) {
                $productImage->setProduct(null);
            }
        }

        return $this;
    }
}
