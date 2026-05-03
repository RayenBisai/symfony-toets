# 🎓 Product CRUD - Symfony Learning Project

Dit project is speciaal gemaakt voor je Symfony examen voorbereiding morgen. Het bevat **alle technieken die je moet beheersen**.

## 📋 Inhoud van dit project

### ✅ Alle vereiste technieken

1. **Controllers maken** ✔
   - `ProductController.php` - CRUD operaties voor producten
   - `CategoryController.php` - CRUD operaties voor categorieën

2. **Views maken en aanpassen (Twig)** ✔
   - `templates/base.html.twig` - Basis template
   - `templates/product/` - Product templates
   - `templates/category/` - Category templates
   - Bootstrap UI voor mooie interface

3. **Models/Entities** ✔
   - `src/Entity/Category.php` - Category entity
   - `src/Entity/Product.php` - Product entity
   - OneToMany/ManyToOne relatie tussen beide

4. **Migraties** ✔
   - `migrations/Version20260503000001.php` - Categories tabel
   - `migrations/Version20260503000002.php` - Products tabel met foreign key

5. **Database in PHPMyAdmin** ✔
   - Automatische tabel creatie via migraties
   - Relaties via foreign keys

6. **.env bestand** ✔
   - `.env` - Database configuratie

7. **Symfony project opstarten** ✔
   - Stappen hieronder

8. **Formulieren** ✔
   - `src/Form/ProductType.php`
   - `src/Form/CategoryType.php`

9. **CREATE - Gegevens toevoegen** ✔
   - `product_create` route - Nieuw product aanmaken
   - `category_create` route - Nieuwe categorie aanmaken

10. **UPDATE - Gegevens wijzigen** ✔
    - `product_edit` route - Product bewerken
    - `category_edit` route - Categorie bewerken

11. **DELETE - Gegevens verwijderen** ✔
    - `product_delete` route - Product verwijderen
    - `category_delete` route - Categorie verwijderen

12. **OneToMany/ManyToOne relatie** ✔
    - Product.php heeft ManyToOne naar Category
    - Category.php heeft OneToMany naar Product
    - Automatische cascade deletion

## 🚀 Hoe te starten

### Stap 1: Database voorbereiding

Zorg dat je **MySQL/MariaDB** draait (bijvoorbeeld via XAMPP).

#### Optie A: Database via PHPMyAdmin aanmaken

1. Open PHPMyAdmin: `http://localhost/phpmyadmin`
2. Klik "Nieuwe database"
3. Naam: `product_crud`
4. Collatie: `utf8mb4_unicode_ci`
5. Klik "Aanmaken"

#### Optie B: Database via MySQL commando

```bash
mysql -u root -p
CREATE DATABASE product_crud CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Stap 2: .env bestand configureren

Edit `.env` en zet je database credentials:

```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/product_crud"
```

Als je password hebt:
```env
DATABASE_URL="mysql://root:jouwwachtwoord@127.0.0.1:3306/product_crud"
```

### Stap 3: Dependencies installeren

```bash
cd product-crud
composer install
```

### Stap 4: Migraties uitvoeren

```bash
php bin/console doctrine:migrations:migrate
```

Dit creëert de tabellen automatisch!

### Stap 5: Symfony server starten

```bash
symfony serve
```

Of:
```bash
php -S localhost:8000 -t public
```

Server draait op: **http://localhost:8000**

## 📚 Project structuur

```
product-crud/
├── src/
│   ├── Entity/
│   │   ├── Category.php          ← Database model
│   │   └── Product.php           ← Database model
│   ├── Controller/
│   │   ├── ProductController.php ← Routes + logica
│   │   └── CategoryController.php ← Routes + logica
│   ├── Form/
│   │   ├── ProductType.php       ← Formulier
│   │   └── CategoryType.php      ← Formulier
│   └── Repository/
│       ├── ProductRepository.php ← Database queries
│       └── CategoryRepository.php ← Database queries
├── templates/
│   ├── base.html.twig            ← Basis layout
│   ├── index.html.twig           ← Home pagina
│   ├── product/
│   │   ├── index.html.twig       ← Producten lijst
│   │   ├── show.html.twig        ← Product detail
│   │   └── form.html.twig        ← Product formulier
│   └── category/
│       ├── index.html.twig       ← Categorieën lijst
│       ├── show.html.twig        ← Category detail
│       └── form.html.twig        ← Category formulier
├── migrations/
│   ├── Version20260503000001.php ← Categories tabel
│   └── Version20260503000002.php ← Products tabel
├── config/
│   ├── packages/
│   │   ├── doctrine.yaml         ← Database config
│   │   ├── framework.yaml        ← Framework config
│   │   └── twig.yaml             ← Template config
│   ├── services.yaml             ← Dependency injection
│   └── routes.yaml               ← Route configuratie
├── .env                          ← Environment vars
└── composer.json                 ← Dependencies
```

## 🔄 CRUD Routes Overzicht

### Producten

| Actie | Route | Method | Controller |
|-------|-------|--------|------------|
| Lijst | `/product/` | GET | `product_index` |
| Detail | `/product/{id}` | GET | `product_show` |
| Aanmaken | `/product/create/new` | GET/POST | `product_create` |
| Bewerken | `/product/{id}/edit` | GET/POST | `product_edit` |
| Verwijderen | `/product/{id}/delete` | POST | `product_delete` |

### Categorieën

| Actie | Route | Method | Controller |
|-------|-------|--------|------------|
| Lijst | `/category/` | GET | `category_index` |
| Detail | `/category/{id}` | GET | `category_show` |
| Aanmaken | `/category/create/new` | GET/POST | `category_create` |
| Bewerken | `/category/{id}/edit` | GET/POST | `category_edit` |
| Verwijderen | `/category/{id}/delete` | POST | `category_delete` |

## 🎯 Belangrijke concepten uitgelegd

### 1. Entities (Models)

```php
// Product heeft ManyToOne relatie met Category
#[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
private ?Category $category = null;

// Category heeft OneToMany relatie met Product
#[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'category')]
private Collection $products;
```

### 2. Controllers

```php
// CREATE
#[Route('/create/new', name: 'product_create', methods: ['GET', 'POST'])]
public function create(Request $request, EntityManagerInterface $entityManager): Response

// READ
#[Route('/', name: 'product_index', methods: ['GET'])]
public function index(ProductRepository $productRepository): Response

// UPDATE
#[Route('/{id}/edit', name: 'product_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response

// DELETE
#[Route('/{id}/delete', name: 'product_delete', methods: ['POST'])]
public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
```

### 3. Forms (Formulieren)

```php
// ProductType extends AbstractType
$builder
    ->add('name', TextType::class)
    ->add('price', MoneyType::class)
    ->add('category', EntityType::class)
    ->add('submit', SubmitType::class);
```

### 4. Migraties

```php
// Migratie creëert tabel en relaties
$this->addSql('CREATE TABLE products (...)');
$this->addSql('ALTER TABLE products ADD CONSTRAINT ... FOREIGN KEY ...');
```

### 5. Templates (Master/Detail)

```twig
{# Master pagina: lijst #}
{% for product in products %}
    <a href="{{ path('product_show', {id: product.id}) }}">{{ product.name }}</a>
{% endfor %}

{# Detail pagina: detail #}
<h1>{{ product.name }}</h1>
<p>{{ product.description }}</p>
```

## 🔍 Basis Commando's

```bash
# Migraties aanmaken (na Entity wijzigingen)
php bin/console make:migration

# Migraties uitvoeren
php bin/console doctrine:migrations:migrate

# Database resetten
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Routes tonen
php bin/console debug:router

# Services tonen
php bin/console debug:autowiring
```

## 💡 Tips voor je examen

1. **Entities eerst!** - Definieer eerst je database modellen
2. **Migraties daarom** - Maak dan de migraties aan
3. **Controllers dan** - Schrijf je routes en logica
4. **Templates last** - Maak je views als laatste

### Entity to Database Flow:
```
Entity klasse → Migrations → Database tabel → Repository queries → Controller → Template
```

## ⚠️ Veelgemaakte fouten

1. **CSRF token missen** in delete formulieren
2. **Relaties niet goed gezet** in entities
3. **EntityManager vergeten** in controller
4. **Twig syntax fout** ({{ }} voor variabelen)
5. **Routes niet geregistreerd** in config

## 🎓 Wat je moet onthouden

- **Entity**: PHP klasse met #[ORM\...] attributes
- **Repository**: Custom query methodes
- **Controller**: Routes en logica
- **Form**: Formulier building
- **Migration**: Database versioning
- **Template**: HTML + Twig
- **Relatie**: OneToMany / ManyToOne / etc.

## 📞 Hulp nodig?

Controleer:
1. `.env` database URL correct?
2. `composer install` uitgevoerd?
3. Migraties gedraait?
4. Server draait?
5. Geen PHP errors in console?

Succes met je examen! 🍀
