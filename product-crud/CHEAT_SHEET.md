# 🚀 SYMFONY CRUD CHEAT SHEET

Snel referentiedocument voor alle code snippets.

---

## ENTITY - Basis Template

```php
<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'mijn_tabelnaam')]
class MijnEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }
}
```

---

## RELATIE - OneToMany / ManyToOne

### Kant: One (Bv: Category)
```php
#[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'category')]
private Collection $products;

public function addProduct(Product $product): static
{
    if (!$this->products->contains($product)) {
        $this->products->add($product);
        $product->setCategory($this);
    }
    return $this;
}

public function removeProduct(Product $product): static
{
    if ($this->products->removeElement($product)) {
        if ($product->getCategory() === $this) {
            $product->setCategory(null);
        }
    }
    return $this;
}

public function getProducts(): Collection { return $this->products; }
```

### Kant: Many (Bv: Product)
```php
#[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
#[ORM\JoinColumn(nullable: false)]
private ?Category $category = null;

public function getCategory(): ?Category { return $this->category; }
public function setCategory(?Category $category): static { $this->category = $category; return $this; }
```

---

## REPOSITORY - Queries

```php
<?php
namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Product::class);
    }

    // Alle records
    public function findAll() {
        return $this->createQueryBuilder('p')
            ->getQuery()
            ->getResult();
    }

    // 1 record op ID
    public function findById($id) {
        return $this->find($id);
    }

    // Met where clause
    public function findByCategory($categoryId) {
        return $this->createQueryBuilder('p')
            ->where('p.category = :cat')
            ->setParameter('cat', $categoryId)
            ->getQuery()
            ->getResult();
    }

    // Met join
    public function findAllWithCategory() {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->addSelect('c')
            ->getQuery()
            ->getResult();
    }
}
```

---

## FORM - Basis Template

```php
<?php
namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{TextType, TextareaType, MoneyType, IntegerType, SubmitType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Naam'])
            ->add('description', TextareaType::class, ['label' => 'Beschrijving'])
            ->add('price', MoneyType::class, ['label' => 'Prijs', 'currency' => 'EUR'])
            ->add('quantity', IntegerType::class, ['label' => 'Voorraad'])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Categorie',
            ])
            ->add('submit', SubmitType::class, ['label' => 'Opslaan']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Product::class]);
    }
}
```

---

## CONTROLLER - Alle CRUD Actions

```php
<?php
namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product')]
class ProductController extends AbstractController
{
    // CREATE
    #[Route('/create/new', name: 'product_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'Aangemaakt!');
            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/form.html.twig', ['form' => $form]);
    }

    // READ (LIST)
    #[Route('/', name: 'product_index', methods: ['GET'])]
    public function index(ProductRepository $repo): Response
    {
        $products = $repo->findAll();
        return $this->render('product/index.html.twig', ['products' => $products]);
    }

    // READ (SHOW)
    #[Route('/{id}', name: 'product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', ['product' => $product]);
    }

    // UPDATE
    #[Route('/{id}/edit', name: 'product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Bijgewerkt!');
            return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
        }

        return $this->render('product/form.html.twig', ['form' => $form]);
    }

    // DELETE
    #[Route('/{id}/delete', name: 'product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $em->remove($product);
            $em->flush();
            $this->addFlash('success', 'Verwijderd!');
        }

        return $this->redirectToRoute('product_index');
    }
}
```

---

## TEMPLATES - Twig Snippets

### BASE TEMPLATE
```twig
<!DOCTYPE html>
<html>
<head>
    <title>{% block title %}{% endblock %}</title>
</head>
<body>
    <nav>
        <a href="{{ path('product_index') }}">Producten</a>
        <a href="{{ path('category_index') }}">Categorieën</a>
    </nav>

    {% for label, messages in app.flashes %}
        <div class="alert alert-{{ label }}">
            {% for message in messages %}{{ message }}{% endfor %}
        </div>
    {% endfor %}

    {% block content %}{% endblock %}
</body>
</html>
```

### FORM TEMPLATE
```twig
{% extends "base.html.twig" %}

{% block content %}
<h1>{{ title }}</h1>
{{ form(form) }}
{% endblock %}
```

### LIST TEMPLATE (Master)
```twig
{% extends "base.html.twig" %}

{% block content %}
<h1>Producten</h1>
<a href="{{ path('product_create') }}">Nieuw</a>

{% for product in products %}
    <div>
        <h3>{{ product.name }}</h3>
        <a href="{{ path('product_show', {id: product.id}) }}">Bekijk</a>
        <a href="{{ path('product_edit', {id: product.id}) }}">Edit</a>
        <form method="POST" action="{{ path('product_delete', {id: product.id}) }}">
            <input type="hidden" name="_token" value="{{ csrf_token('delete' ~ product.id) }}">
            <button type="submit">Delete</button>
        </form>
    </div>
{% endfor %}
{% endblock %}
```

### DETAIL TEMPLATE (Detail)
```twig
{% extends "base.html.twig" %}

{% block content %}
<h1>{{ product.name }}</h1>
<p>{{ product.description }}</p>
<p>€{{ product.price }}</p>
<p>Categorie: {{ product.category.name }}</p>

<a href="{{ path('product_index') }}">Terug</a>
<a href="{{ path('product_edit', {id: product.id}) }}">Bewerk</a>
{% endblock %}
```

---

## MIGRATIES

### Tabel aanmaken
```php
$this->addSql('CREATE TABLE products (
    id INT AUTO_INCREMENT NOT NULL,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    price DOUBLE PRECISION NOT NULL,
    created_at DATETIME IMMUTABLE NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
)');
```

### Kolom toevoegen
```php
$this->addSql('ALTER TABLE products ADD description LONGTEXT NOT NULL');
```

### Foreign key toevoegen
```php
$this->addSql('ALTER TABLE products ADD CONSTRAINT FK_CAT 
    FOREIGN KEY (category_id) REFERENCES categories(id)');
```

---

## COMMANDO'S

```bash
# Migratie maken
php bin/console make:migration

# Migratie uitvoeren
php bin/console doctrine:migrations:migrate

# Database resetten
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Routes tonen
php bin/console debug:router

# Services tonen
php bin/console debug:autowiring

# Server starten
symfony serve

# Seed database (dummy data)
php bin/console doctrine:fixtures:load
```

---

## VALIDATORS (Validatie in Entity)

```php
use Symfony\Component\Validator\Constraints as Assert;

class Product
{
    #[Assert\NotBlank]
    private ?string $name = null;

    #[Assert\Email]
    private ?string $email = null;

    #[Assert\Positive]
    #[Assert\LessThanOrEqual(999999)]
    private ?float $price = null;

    #[Assert\Length(min: 10, max: 255)]
    private ?string $description = null;

    #[Assert\Unique]
    private ?string $sku = null;
}
```

---

## DOCTRINE ATTRIBUTES (Alle veld types)

```php
#[ORM\Column]                    // Standaard
#[ORM\Column(length: 100)]       // VARCHAR(100)
#[ORM\Column(type: 'text')]      // LONGTEXT
#[ORM\Column(type: 'integer')]   // INT
#[ORM\Column(type: 'float')]     // FLOAT
#[ORM\Column(type: 'boolean')]   // BOOLEAN
#[ORM\Column(type: 'datetime_immutable')] // DATETIME
#[ORM\Column(nullable: true)]    // NULL allowed
#[ORM\Column(unique: true)]      // UNIQUE
```

---

## RELATIONS

```php
// ManyToOne (veel naar 1)
#[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
#[ORM\JoinColumn(nullable: false)]
private ?Category $category = null;

// OneToMany (1 naar veel)
#[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'category', cascade: ['remove'])]
private Collection $products;

// ManyToMany
#[ORM\ManyToMany(targetEntity: Tag::class)]
private Collection $tags;

// OneToOne
#[ORM\OneToOne(targetEntity: Profile::class, inversedBy: 'user')]
private ?Profile $profile = null;
```

---

Veel succes! 🎓
