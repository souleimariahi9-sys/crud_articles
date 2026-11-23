<?php
namespace App\Controller;

use App\Service\ArticleStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;

#[Route('/articles')]
class ArticleController extends AbstractController
{
    public function __construct(private ArticleStorage $storage) {}

    #[Route('/', name: 'article_index')]
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $this->storage->findAll(),
        ]);
    }

    #[Route('/show/{id}', name: 'article_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        $article = $this->storage->find($id);
        if (!$article) throw $this->createNotFoundException('Article introuvable');
        return $this->render('article/show.html.twig', ['article' => $article]);
    }

    #[Route('/add', name: 'article_add')]
    public function add(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->storage->add(
                $request->request->get('titre'),
                $request->request->get('contenu'),
                $request->request->get('auteur')
            );
            return $this->redirectToRoute('article_index');
        }
        return $this->render('article/add.html.twig');
    }

    #[Route('/edit/{id}', name: 'article_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request): Response
    {
        $article = $this->storage->find($id);
        if (!$article) throw $this->createNotFoundException('Article introuvable');

        if ($request->isMethod('POST')) {
            $this->storage->update(
                $id,
                $request->request->get('titre'),
                $request->request->get('contenu'),
                $request->request->get('auteur')
            );
            return $this->redirectToRoute('article_index');
        }
        return $this->render('article/edit.html.twig', ['article' => $article]);
    }

    #[Route('/delete/{id}', name: 'article_delete', requirements: ['id' => '\d+'])]
    public function delete(int $id): Response
    {
        $this->storage->delete($id);
        return $this->redirectToRoute('article_index');
    }
}