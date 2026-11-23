<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ArticleStorage
{
    private string $file;

    public function __construct(#[Autowire('%kernel.project_dir%')] string $projectDir)
    {
        $this->file = $projectDir . '/data/articles.json';
    }

    public function findAll(): array
    {
        return json_decode(file_get_contents($this->file), true) ?? [];
    }

    public function find(int $id): ?array
    {
        foreach ($this->findAll() as $a) {
            if ($a['id'] === $id) return $a;
        }
        return null;
    }

    public function add(string $titre, string $contenu, string $auteur): void
    {
        $articles = $this->findAll();
        $newId = $articles ? max(array_column($articles, 'id')) + 1 : 1;
        $articles[] = [
            'id' => $newId, 'titre' => $titre, 'contenu' => $contenu,
            'auteur' => $auteur, 'date' => date('Y-m-d'),
        ];
        $this->save($articles);
    }

    public function update(int $id, string $titre, string $contenu, string $auteur): void
    {
        $articles = $this->findAll();
        foreach ($articles as &$a) {
            if ($a['id'] === $id) {
                $a['titre'] = $titre;
                $a['contenu'] = $contenu;
                $a['auteur'] = $auteur;
            }
        }
        $this->save($articles);
    }

    public function delete(int $id): void
    {
        $articles = array_filter($this->findAll(), fn($a) => $a['id'] !== $id);
        $this->save(array_values($articles));
    }

    private function save(array $articles): void
    {
        file_put_contents($this->file, json_encode($articles, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}