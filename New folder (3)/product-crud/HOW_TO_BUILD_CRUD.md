# 🎓 HOE JE ZELF EEN CRUD BOUWT - STAP VOOR STAP

Dit document walkt je door het bouwen van een complete CRUD van nul af.

---

## 🎯 SCENARIO: Maak een CRUD voor "Comments" (opmerkingen)

Wij gaan een Comments CRUD bouwen bij een artikel systeem.

---

## FASE 1: ENTITIES (Models)

### Stap 1a: Comment Entity Aanmaken

```php
<?php
// src/Entity/Comment.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'comments')]
class Comment
{
    // ID (altijd eerste!)
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Tekst van comment
    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $content = null;

    // Auteur van comment
    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $author = null;

    // Datum aangemaakt
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // ManyToOne: veel comments → 1 artikel
    #[ORM\ManyToOne(targetEntity: Article::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null;

    // CONSTRUCTOR
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // GETTERS & SETTERS
    public function getId(): ?int { return $this->id; }

    public function getContent(): ?string { return $this->content; }
    public function setContent(string $content): static { 
        $this->content = $content; 
        return $this; 
    }

    public function getAuthor(): ?string { return $this->author; }
    public function setAuthor(string $author): static { 
        $this->author = $author; 
        return $this; 
    }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { 
        $this->createdAt = $createdAt; 
        return $this; 
    }

    public function getArticle(): ?Article { return $this->article; }
    public function setArticle(?Article $article): static { 
        $this->article = $article; 
        return $this; 
    }
}
```

### Stap 1b: Article Entity Aanpassen (OneToMany)

```php
<?php
// src/Entity/Article.php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'articles')]
class Article
{
    // ... andere properties ...

    // OneToMany: 1 artikel → veel comments
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'article', cascade: ['remove'])]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getComments(): Collection { return $this->comments; }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setArticle($this);
        }
        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }
        return $this;
    }
}
```

---

## FASE 2: MIGRATIES (Database)

### Stap 2: Migratie Aanmaken en Uitvoeren

```bash
# Stap 1: Genereer migratie op basis van entities
php bin/console make:migration

# Output: Migration created successfully: migrations/Version20260503123456.php
```

### Gegenereerde Migratie (Check deze!)

```php
<?php
// migrations/Version20260503123456.php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260503123456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Comment entity';
    }

    public function up(Schema $schema): void
    {
        // Symfony genereert dit automatisch!
        $this->addSql('CREATE TABLE comments (
            id INT AUTO_INCREMENT NOT NULL,
            article_id INT NOT NULL,
            content LONGTEXT NOT NULL,
            author VARCHAR(100) NOT NULL,
            created_at DATETIME IMMUTABLE NOT NULL,
            PRIMARY KEY(id),
            KEY FK_ARTICLE (article_id),
            CONSTRAINT FK_ARTICLE FOREIGN KEY (article_id) REFERENCES articles(id)
        ) CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE comments');
    }
}
```

### Stap 3: Migratie Uitvoeren

```bash
# Voer migratie uit (creëert tabel in DB)
php bin/console doctrine:migrations:migrate

# Output: [notice] Migrating up to DoctrineMigrations\Version20260503123456
# [notice] finished
```

---

## FASE 3: REPOSITORY

### Stap 3: CommentRepository Aanmaken

```php
<?php
// src/Repository/CommentRepository.php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    // Alle comments van artikel
    public function findByArticle($articleId)
    {
        return $this->createQueryBuilder('c')
            ->where('c.article = :article')
            ->setParameter('article', $articleId)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // Recente comments
    public function findRecent($limit = 10)
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function save(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
```

---

## FASE 4: FORM

### Stap 4: CommentType Formulier

```php
<?php
// src/Form/CommentType.php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('author', TextType::class, [
                'label' => 'Uw naam',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Jan Jansen'],
                'constraints' => [new NotBlank()],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Uw opmerking',
                'attr' => ['class' => 'form-control', 'rows' => 5, 'placeholder' => 'Schrijf hier...'],
                'constraints' => [new NotBlank()],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Verstuur Opmerking',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
```

---

## FASE 5: CONTROLLER

### Stap 5: CommentController met alle CRUD

```php
<?php
// src/Controller/CommentController.php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Article;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{
    /**
     * CREATE - Voeg nieuwe comment toe
     */
    #[Route('/article/{articleId}/add', name: 'comment_create', methods: ['POST'])]
    public function create(
        Request $request, 
        int $articleId,
        EntityManagerInterface $em
    ): Response {
        $article = $em->getRepository(Article::class)->find($articleId);
        
        if (!$article) {
            throw $this->createNotFoundException('Artikel niet gevonden');
        }

        $comment = new Comment();
        $comment->setArticle($article);
        
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Opmerking toegevoegd!');
            return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
        }

        return $this->render('comment/form.html.twig', [
            'form' => $form,
            'article' => $article,
        ]);
    }

    /**
     * READ - Alle comments van artikel
     */
    #[Route('/article/{articleId}', name: 'comment_index', methods: ['GET'])]
    public function indexByArticle(
        int $articleId,
        CommentRepository $repo,
        EntityManagerInterface $em
    ): Response {
        $article = $em->getRepository(Article::class)->find($articleId);
        
        if (!$article) {
            throw $this->createNotFoundException('Artikel niet gevonden');
        }

        $comments = $repo->findByArticle($articleId);

        return $this->render('comment/index.html.twig', [
            'comments' => $comments,
            'article' => $article,
        ]);
    }

    /**
     * READ - Één comment detail
     */
    #[Route('/{id}', name: 'comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * UPDATE - Wijzig comment
     */
    #[Route('/{id}/edit', name: 'comment_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Comment $comment,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Opmerking bijgewerkt!');
            return $this->redirectToRoute('comment_show', ['id' => $comment->getId()]);
        }

        return $this->render('comment/form.html.twig', [
            'form' => $form,
            'comment' => $comment,
        ]);
    }

    /**
     * DELETE - Verwijder comment
     */
    #[Route('/{id}/delete', name: 'comment_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Comment $comment,
        EntityManagerInterface $em
    ): Response {
        $articleId = $comment->getArticle()->getId();

        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $em->remove($comment);
            $em->flush();

            $this->addFlash('success', 'Opmerking verwijderd!');
        }

        return $this->redirectToRoute('article_show', ['id' => $articleId]);
    }
}
```

---

## FASE 6: TEMPLATES

### Template 1: Form

```twig
{# templates/comment/form.html.twig #}

{% extends "base.html.twig" %}

{% block title %}Opmerking Toevoegen{% endblock %}

{% block content %}
<div class="card">
    <div class="card-header">
        <h3>Voeg een opmerking toe</h3>
    </div>
    <div class="card-body">
        {{ form(form) }}
    </div>
</div>
{% endblock %}
```

### Template 2: List

```twig
{# templates/comment/index.html.twig #}

{% extends "base.html.twig" %}

{% block title %}Opmerkingen: {{ article.title }}{% endblock %}

{% block content %}
<h1>Opmerkingen ({{ article.comments|length }})</h1>

<div class="comments-list">
    {% for comment in article.comments %}
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    {{ comment.author }}
                    <small class="text-muted">- {{ comment.createdAt|date('d-m-Y H:i') }}</small>
                </h5>
                <p class="card-text">{{ comment.content }}</p>
                
                <a href="{{ path('comment_edit', {id: comment.id}) }}" class="btn btn-sm btn-warning">Bewerk</a>
                
                <form method="POST" action="{{ path('comment_delete', {id: comment.id}) }}" style="display:inline;">
                    <input type="hidden" name="_token" value="{{ csrf_token('delete' ~ comment.id) }}">
                    <button class="btn btn-sm btn-danger">Verwijder</button>
                </form>
            </div>
        </div>
    {% else %}
        <p class="text-muted">Geen opmerkingen nog.</p>
    {% endfor %}
</div>

<a href="{{ path('comment_create', {articleId: article.id}) }}" class="btn btn-primary mt-4">Voeg Opmerking Toe</a>
{% endblock %}
```

### Template 3: Detail

```twig
{# templates/comment/show.html.twig #}

{% extends "base.html.twig" %}

{% block title %}Opmerking{{ endblock %}

{% block content %}
<div class="card">
    <div class="card-body">
        <h3>{{ comment.author }}</h3>
        <p class="text-muted">{{ comment.createdAt|date('d-m-Y H:i') }}</p>
        <p>{{ comment.content }}</p>
        
        <a href="{{ path('comment_edit', {id: comment.id}) }}" class="btn btn-warning">Bewerk</a>
        <a href="{{ path('article_show', {id: comment.article.id}) }}" class="btn btn-secondary">Terug</a>
    </div>
</div>
{% endblock %}
```

---

## FASE 7: INTEGRATIE IN ARTICLE TEMPLATE

### Voeg comments toe aan artikel detail

```twig
{# templates/article/show.html.twig #}

{% extends "base.html.twig" %}

{% block content %}
<h1>{{ article.title }}</h1>
<p>{{ article.content }}</p>

<!-- COMMENTS SECTIE -->
<hr>
<h2>Opmerkingen ({{ article.comments|length }})</h2>

<div class="comments-section">
    {% for comment in article.comments %}
        <div class="card mb-2">
            <div class="card-body">
                <strong>{{ comment.author }}</strong>
                <small class="text-muted">- {{ comment.createdAt|date('d-m-Y') }}</small>
                <p>{{ comment.content }}</p>
            </div>
        </div>
    {% endfor %}
</div>

<!-- COMMENT FORM -->
<h3>Voeg een opmerking toe:</h3>
<form method="POST" action="{{ path('comment_create', {articleId: article.id}) }}">
    <!-- Hier de form_start en form_end -->
</form>

<a href="{{ path('article_index') }}">Terug naar artikelen</a>
{% endblock %}
```

---

## COMPLETE CHECKLIST

- ✅ Entity `Comment` aangemaakt met properties
- ✅ Entity `Article` aangepasst met OneToMany
- ✅ Migratie aangemaakt: `make:migration`
- ✅ Migratie uitgevoerd: `doctrine:migrations:migrate`
- ✅ Repository met custom queries
- ✅ Form `CommentType` aangemaakt
- ✅ Controller met alle CRUD routes
- ✅ Templates (form, index, show)
- ✅ Integratie in artikel template

---

## TEST IN BROWSER

1. Ga naar artikel detail: `http://localhost:8000/article/1`
2. Voeg comment toe via formulier
3. Bekijk alle comments
4. Bewerk een comment
5. Verwijder een comment

---

## ⚠️ HÄUFIGE FEHLER

| Fout | Oorzaak | Oplossing |
|------|---------|----------|
| "Entity not found" | Repository lookup failed | Check ID + DB tabel |
| "No route found" | Route niet geregistreerd | Zorg path() argument klopt |
| "Form not rendering" | Form type fout | Check `{{ form() }}` |
| CSRF error op delete | Missing token | Zorg token in form |

---

Je bent klaar! Tijd om dit zelf te doen! 💪
