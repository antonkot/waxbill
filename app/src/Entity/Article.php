<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\ApiResource\ArticleProcessor;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(processor: ArticleProcessor::class),
        new Put(processor: ArticleProcessor::class),
        new Delete()
    ],
    normalizationContext: ['groups' => ['article:read']],
    denormalizationContext: ['groups' => ['article:write']],
    paginationEnabled: false,
)]
#[ApiFilter(SearchFilter::class, properties: ['articleTags.tag.id' => 'exact'])]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['article:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['article:read', 'article:write'])]
    private ?string $title = null;

    /**
     * @var Collection<int, ArticleTag>
     */
    #[ORM\OneToMany(targetEntity: ArticleTag::class, mappedBy: 'article', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['article:read'])]
    private Collection $articleTags;

    /**
     * @var int[] $tags
     */
    #[Groups(['article:read', 'article:write'])]
    private array $tags = [];

    public function __construct()
    {
        $this->articleTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, ArticleTag>
     */
    public function getArticleTags(): Collection
    {
        return $this->articleTags;
    }

    /**
     * @param Collection $articleTags
     * @return Article
     */
    public function setArticleTags(Collection $articleTags): self
    {
        $this->articleTags = $articleTags;
        return $this;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }
}
