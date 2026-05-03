# 📖 STUDY GUIDE - Symfony CRUD Uitleg

Dit document bevat alle belangrijke concepten uitgelegd voor je examen morgen.

---

## 1️⃣ ENTITIES (Models/Database Klassen)

### Wat is een Entity?
Een Entity is een PHP klasse die een database tabel vertegenwoordigt. Elke eigenschap = een kolom in de tabel.

### Voorbeeld: Category Entity

```php
<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'categories')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    // Getters en setters...
}
```

### Attributes uitgelegd:
- `#[ORM\Entity]` = Dit is een database entity
- `#[ORM\Table(name: 'categories')]` = Tabel naam
- `#[ORM\Id]` = Dit is de primary key
- `#[ORM\GeneratedValue]` = Auto-increment
- `#[ORM\Column(length: 100)]` = Kolom met max 100 karakters

---

## 2️⃣ RELATIES (OneToMany / ManyToOne)

### OneToMany / ManyToOne uitgelegd

```
1 Categorie → Veel Producten
↓
Category heeft OneToMany naar Product
Product heeft ManyToOne naar Category
```

### In Code:

**Category.php (One):**
```php
/**
 * @var Collection<int, Product>
 */
#[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'category', cascade: ['remove'])]
private Collection $products;

public function addProduct(Product $product): static
{
    if (!$this->products->contains($product)) {
        $this->products->add($product);
        $product->setCategory($this);
    }
    return $this;
}
```

**Product.php (Many):**
```php
#[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
#[ORM\JoinColumn(nullable: false)]
private ?Category $category = null;

public function setCategory(?Category $category): static
{
    $this->category = $category;
    return $this;
}
```

### Wat betekent dit in de database?

**Categories tabel:**
```
id | name
---|-----
1  | Elektronica
2  | Boeken
```

**Products tabel:**
```
id | name    | category_id
---|---------|----------
1  | Laptop  | 1
2  | Mouse   | 1
3  | PHP Book| 2
```

---

## 3️⃣ MIGRATIES (Database Versioning)

### Wat is een migratie?
Een migratie is een PHP bestand dat database wijzigingen versieert. Met migraties kun je:
- Tabel creëren
- Kolommen toevoegen
- Relaties aanmaken
- Database state beheren

### Migratie aanmaken (normaal):

```bash
# Stap 1: Entity wijzigen (bv. nieuwe eigenschap)
# Stap 2: Migratie genereren
php bin/console make:migration

# Stap 3: Migratie uitvoeren
php bin/console doctrine:migrations:migrate
```

### Migratie bestand voorbeeld:

```php
<?php
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260503000001 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // Database upgrade code
        $this->addSql('CREATE TABLE categories (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(100) NOT NULL,
            PRIMARY KEY(id)
        )');
    }

    public function down(Schema $schema): void
    {
        // Database downgrade code
        $this->addSql('DROP TABLE categories');
    }
}
```

---

## 4️⃣ REPOSITORIES (Database Queries)

### Wat is een Repository?
Een Repository bevat custom query methodes om data uit de database te halen.

### Voorbeeld: ProductRepository

```php
<?php
namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    // Query: alle producten van categorie 1
    public function findByCategory($categoryId)
    {
        return $this->createQueryBuilder('p')
            ->where('p.category = :category')
            ->setParameter('category', $categoryId)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // Query: product met ID 5
    public function findOneById($id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    // Alle producten
    public function findAll()
    {
        return $this->findAll();
    }
}
```

### QueryBuilder uitgelegd:

```php
$this->createQueryBuilder('p')  // p = alias voor Product
    ->select('p')                // SELECT p
    ->from('Product', 'p')       // FROM products p
    ->where('p.price > :price')  // WHERE price > 100
    ->setParameter('price', 100)
    ->orderBy('p.name', 'ASC')   // ORDER BY name ASC
    ->getQuery()                 // Zet in Query object
    ->getResult();               // Execute + return resultaten
```

---

## 5️⃣ CONTROLLERS (Routes & Logica)

### Wat is een Controller?
Een Controller verbindt Routes met logica en geeft responses terug.

### Voorbeeld: ProductController

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
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();
            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/form.html.twig', [
            'form' => $form,
        ]);
    }

    // READ (lijst)
    #[Route('/', name: 'product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    // READ (detail)
    #[Route('/{id}', name: 'product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    // UPDATE
    #[Route('/{id}/edit', name: 'product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
        }

        return $this->render('product/form.html.twig', [
            'form' => $form,
            'product' => $product,
        ]);
    }

    // DELETE
    #[Route('/{id}/delete', name: 'product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index');
    }
}
```

### Route Attributes uitgelegd:

```php
#[Route('/product')]              // Base route
#[Route('/', name: 'product_index')]     // Volledige URL: /product/
                                         // Naam: product_index

#[Route('/{id}')]                // {id} = parameter
                                // /product/5 → $product param

#[Route('/create/new')]          // Fixed path
                                // /product/create/new

#[Route('/', methods: ['GET'])]   // Alleen GET requests
#[Route('/', methods: ['POST'])]  // Alleen POST requests
#[Route('/', methods: ['GET', 'POST'])]  // GET en POST
```

---

## 6️⃣ FORMS (Formulieren)

### Wat is een Form?
Een Form genereert HTML formulieren en valideert data automatisch.

### Voorbeeld: ProductType

```php
<?php
namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // TextType
            ->add('name', TextType::class, [
                'label' => 'Productnaam',
                'attr' => ['placeholder' => 'Voer naam in'],
            ])

            // EntityType (dropdown met categories)
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Selecteer categorie',
            ])

            // MoneyType
            ->add('price', MoneyType::class, [
                'currency' => 'EUR',
            ])

            // SubmitType
            ->add('submit', SubmitType::class, [
                'label' => 'Opslaan',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
```

### In Controller gebruiken:

```php
$product = new Product();
$form = $this->createForm(ProductType::class, $product);
$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
    // Form data is geldig
    $entityManager->persist($product);
    $entityManager->flush();
}
```

---

## 7️⃣ TEMPLATES (Twig Views)

### Twig Syntax

```twig
{# Commentaar #}

{{ variableName }}          {# Print variable #}

{% for product in products %}
    {{ product.name }}
{% endfor %}

{% if product %}
    <p>Product bestaat</p>
{% else %}
    <p>Geen product</p>
{% endif %}

<a href="{{ path('product_show', {id: product.id}) }}">
    Klik hier
</a>

{{ form(form) }}            {# Render form #}
{{ form_widget(form) }}     {# Render form fields #}
{{ form_errors(form) }}     {# Render form errors #}

{% extends "base.html.twig" %}  {# Inherit template #}
{% block content %}             {# Define block #}
{% endblock %}
```

### Master/Detail Template Voorbeeld:

**products/index.html.twig (Master - Lijst):**
```twig
{% for product in products %}
    <div>
        <h3>{{ product.name }}</h3>
        <a href="{{ path('product_show', {id: product.id}) }}">
            Details →
        </a>
    </div>
{% endfor %}
```

**products/show.html.twig (Detail - Detail):**
```twig
<h1>{{ product.name }}</h1>
<p>{{ product.description }}</p>
<p>€{{ product.price }}</p>
<a href="{{ path('product_index') }}">Terug naar lijst</a>
```

---

## 8️⃣ COMPLETE CRUD FLOW

### 1. CREATE (Toevoegen)

```
GET /product/create/new
  ↓
ProductController::create()
  ↓
Maak leeg Product object
Maak ProductType formulier
  ↓
Render product/form.html.twig
  ↓
Gebruiker vult in en submit
  ↓
POST /product/create/new
  ↓
form->handleRequest()
  ↓
$entityManager->persist($product)
$entityManager->flush()
  ↓
Redirect naar product_index
```

### 2. READ (Lezen)

**Lijst:**
```
GET /product/
  ↓
ProductController::index()
  ↓
$productRepository->findAll()
  ↓
Render product/index.html.twig met alle producten
```

**Detail:**
```
GET /product/5
  ↓
ProductController::show()
  ↓
Symfony paraconverter: haal Product met id=5 op
  ↓
Render product/show.html.twig met 1 product
```

### 3. UPDATE (Wijzigen)

```
GET /product/5/edit
  ↓
ProductController::edit()
  ↓
Load Product met id=5
Maak ProductType formulier (pre-filled met data)
  ↓
Render product/form.html.twig
  ↓
Gebruiker wijzigt en submit
  ↓
POST /product/5/edit
  ↓
form->handleRequest()
  ↓
$entityManager->flush() (UPDATE in DB)
  ↓
Redirect naar product_show
```

### 4. DELETE (Verwijderen)

```
<form method="POST" action="{{ path('product_delete', {id: 5}) }}">
  <input name="_token" value="...">
  <button>Verwijder</button>
</form>
  ↓
POST /product/5/delete
  ↓
ProductController::delete()
  ↓
CSRF token validatie
  ↓
$entityManager->remove($product)
$entityManager->flush() (DELETE in DB)
  ↓
Redirect naar product_index
```

---

## 🎯 CHECKLIST: Stappen voor eigen CRUD

1. ✅ **Entity** - Maak Entity klasse met alle properties
2. ✅ **Relatie** - Voeg relatie toe (ManyToOne/OneToMany)
3. ✅ **Repository** - Voeg custom queries toe
4. ✅ **Migratie** - Genereer: `php bin/console make:migration`
5. ✅ **Migratie uitvoeren** - `php bin/console doctrine:migrations:migrate`
6. ✅ **Form** - Maak FormType klasse
7. ✅ **Controller** - Maak alle 5 actions (index, show, create, edit, delete)
8. ✅ **Templates** - Maak alle twig templates

---

## ⚠️ Veelgemaakte Fouten

### 1. Relatie vergeten
```php
// ❌ FOUT
class Product {
    private Category $category;
}

// ✅ GOED
class Product {
    #[ORM\ManyToOne(targetEntity: Category::class)]
    private ?Category $category = null;
}
```

### 2. EntityManager vergeten
```php
// ❌ FOUT
$product = new Product();
$product->setName('Test');
// Niets opgeslagen!

// ✅ GOED
$product = new Product();
$product->setName('Test');
$entityManager->persist($product);
$entityManager->flush();
```

### 3. CSRF token missen
```php
// ❌ FOUT
<form method="POST" action="/product/1/delete">
    <button>Delete</button>
</form>

// ✅ GOED
<form method="POST" action="/product/1/delete">
    <input type="hidden" name="_token" value="{{ csrf_token('delete1') }}">
    <button>Delete</button>
</form>
```

### 4. Route parameter niet verklaard
```php
// ❌ FOUT
#[Route('/{id}/edit')]
public function edit($id) {}  // Waar komt $id vandaan?

// ✅ GOED
#[Route('/{id}/edit')]
public function edit(Product $product) {}  // Symfony converteert {id} naar Product
```

---

## 📚 Samenvatting

```
DATABASE FLOW:
==============
Entity
  ↓
Migratie (SQL creëert tabel)
  ↓
Database tabel
  ↓
Repository (queries)
  ↓
Controller (logica)
  ↓
Form (validatie + HTML)
  ↓
Template (weergave)
```

Je bent klaar voor je examen! 🚀
