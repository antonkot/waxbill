<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Article;
use App\Entity\ArticleTag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;

class ArticleProcessor implements ProcessorInterface
{
    private EntityManagerInterface $entityManager;
    private TagRepository $tagRepository;

    public function __construct(EntityManagerInterface $entityManager, TagRepository $tagRepository)
    {
        $this->entityManager = $entityManager;
        $this->tagRepository = $tagRepository;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Article
    {
        if (!$data instanceof Article) {
            throw new \Exception('Data must be an instance of Article');
        }

        $tagIds = $data->getTags() ?? [];

        foreach ($data->getArticleTags() as $articleTag) {
            $this->entityManager->remove($articleTag);
        }

        foreach ($tagIds as $tagId) {
            $tag = $this->tagRepository->find($tagId);
            if ($tag) {
                $articleTag = new ArticleTag();
                $articleTag->setArticle($data);
                $articleTag->setTag($tag);
                $this->entityManager->persist($articleTag);
            }
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}
