# 🧪 ZELFTEST - Controleer je Kennis

Beantwoord deze vragen voordat je naar je examen gaat!

---

## SECTION 1: ENTITIES

### Q1.1: Entities Basis
**Vraag:** Wat is een Entity en waarom gebruiken we attributes?

**Jouw antwoord:**
```
_________________________________
```

**Correct antwoord:**
```
Entity = PHP klasse die 1 database tabel vertegenwoordigt
Attributes = Metadata (#[ORM\...]) die zeggen hoe data naar DB gaat
```

### Q1.2: Entity Property
**Vraag:** Voeg een tekstkolom toe met max 255 karakters:

**Jouw code:**
```php
_________________________________
```

**Correct antwoord:**
```php
#[ORM\Column(length: 255)]
private ?string $title = null;
```

### Q1.3: Getters/Setters
**Vraag:** Wat is het verschil tussen getter en setter?

**Jouw antwoord:**
```
Getter: ________________________________
Setter: ________________________________
```

**Correct antwoord:**
```
Getter: Haalt waarde op (public function get)
Setter: Zet waarde (public function set, returns $this)
```

---

## SECTION 2: RELATIES

### Q2.1: OneToMany
**Vraag:** 1 categorie kan veel producten hebben. Hoe ziet OneToMany eruit?

**Jouw code:**
```php
_________________________________
```

**Correct antwoord:**
```php
#[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'category')]
private Collection $products;
```

### Q2.2: ManyToOne
**Vraag:** Veel producten behoren tot 1 categorie. Hoe ziet ManyToOne eruit?

**Jouw code:**
```php
_________________________________
```

**Correct antwoord:**
```php
#[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
private ?Category $category = null;
```

### Q2.3: Relatie Begrijpen
**Vraag:** Welke kant is "owning side" en welke "inverse side"?

**Jouw antwoord:**
```
Owning side: ____________________
Inverse side: ___________________
```

**Correct antwoord:**
```
Owning side: ManyToOne (heeft FK in DB) = Product
Inverse side: OneToMany (geen FK) = Category
```

---

## SECTION 3: MIGRATIES

### Q3.1: Migratie Aanmaken
**Vraag:** Welk commando om migratie te genereren?

**Jouw antwoord:**
```bash
_________________________________
```

**Correct antwoord:**
```bash
php bin/console make:migration
```

### Q3.2: Migratie Uitvoeren
**Vraag:** Welk commando om migratie uit te voeren?

**Jouw antwoord:**
```bash
_________________________________
```

**Correct antwoord:**
```bash
php bin/console doctrine:migrations:migrate
```

### Q3.3: Wat Doet Migratie?
**Vraag:** Wat gebeurt er in de up() en down() methode?

**Jouw antwoord:**
```
up():   ___________________________
down(): ____________________________
```

**Correct antwoord:**
```
up():   Database upgrade (tabellen creëren)
down(): Database downgrade (wijzigingen terugdraaien)
```

---

## SECTION 4: REPOSITORY

### Q4.1: Custom Query
**Vraag:** Hoe haal je alle producten van categorie ID 5 op?

**Jouw code:**
```php
_________________________________
```

**Correct antwoord:**
```php
return $this->createQueryBuilder('p')
    ->where('p.category = :cat')
    ->setParameter('cat', 5)
    ->getQuery()
    ->getResult();
```

### Q4.2: QueryBuilder
**Vraag:** Wat betekenen: createQueryBuilder, where, getQuery, getResult?

**Jouw antwoord:**
```
createQueryBuilder: ________________
where: ______________________________
getQuery: ____________________________
getResult: ____________________________
```

**Correct antwoord:**
```
createQueryBuilder: Start SQL query builder
where: Zet WHERE clause
getQuery: Zet in Query object
getResult: Execute + return resultaten
```

---

## SECTION 5: CONTROLLER

### Q5.1: Route Definieren
**Vraag:** Voeg CREATE route toe op `/product/create/new`:

**Jouw code:**
```php
#[Route('/product')]
_________________________________
public function create(Request $request, EntityManagerInterface $em) { ... }
```

**Correct antwoord:**
```php
#[Route('/product')]
#[Route('/create/new', name: 'product_create', methods: ['GET', 'POST'])]
public function create(Request $request, EntityManagerInterface $em) { ... }
```

### Q5.2: CREATE Logica
**Vraag:** Wat zijn de 3 stappen in CREATE action?

**Jouw antwoord:**
```
1. ___________________________
2. ___________________________
3. ___________________________
```

**Correct antwoord:**
```
1. Maak leeg object + formulier
2. Handle request (als POST + valid)
3. Persist + flush naar DB
```

### Q5.3: DELETE CSRF
**Vraag:** Waarom is CSRF token nodig in DELETE?

**Jouw antwoord:**
```
_________________________________
```

**Correct antwoord:**
```
Veiligheid - voorkomt ongeautoriseerde verwijderingen
```

---

## SECTION 6: FORMS

### Q6.1: Form Type
**Vraag:** Hoe voeg je dropdown field toe voor Category?

**Jouw code:**
```php
_________________________________
```

**Correct antwoord:**
```php
->add('category', EntityType::class, [
    'class' => Category::class,
    'choice_label' => 'name',
])
```

### Q6.2: Validatie
**Vraag:** Hoe zorg je dat "name" field niet leeg kan zijn?

**Jouw code:**
```php
_________________________________
```

**Correct antwoord:**
```php
#[Assert\NotBlank]
private ?string $name = null;
```

---

## SECTION 7: TEMPLATES

### Q7.1: Twig Loop
**Vraag:** Toon alle producten in een loop:

**Jouw code:**
```twig
_________________________________
```

**Correct antwoord:**
```twig
{% for product in products %}
    <h3>{{ product.name }}</h3>
{% endfor %}
```

### Q7.2: Twig Link
**Vraag:** Maak link naar product detail (ID 5):

**Jouw code:**
```twig
_________________________________
```

**Correct antwoord:**
```twig
<a href="{{ path('product_show', {id: 5}) }}">Detail</a>
```

### Q7.3: Twig Form
**Vraag:** Hoe render je een formulier?

**Jouw code:**
```twig
_________________________________
```

**Correct antwoord:**
```twig
{{ form(form) }}
```

---

## SECTION 8: COMPLETE FLOW

### Q8.1: CREATE Flow
**Vraag:** Wat is de volledige CREATE flow?

**Jouw antwoord:**
```
1. ____________________________
2. ____________________________
3. ____________________________
4. ____________________________
5. ____________________________
```

**Correct antwoord:**
```
1. GET /product/create/new
2. ProductController::create() → render form
3. Gebruiker vult in + submit
4. POST /product/create/new
5. persist() + flush() → DB + redirect
```

### Q8.2: UPDATE vs CREATE
**Vraag:** Wat is het verschil tussen UPDATE en CREATE flow?

**Jouw antwoord:**
```
_________________________________
```

**Correct antwoord:**
```
CREATE: Leeg object + formulier
UPDATE: Bestaand object + pre-filled formulier
```

---

## SECTION 9: DATABASE

### Q9.1: .env Configuratie
**Vraag:** Hoe zet je database URL in .env?

**Jouw code:**
```
_________________________________
```

**Correct antwoord:**
```
DATABASE_URL="mysql://root:password@127.0.0.1:3306/databasenaam"
```

### Q9.2: Database Aanmaken
**Vraag:** 2 manieren om database aan te maken:

**Manier 1:**
```
_________________________________
```

**Manier 2:**
```bash
_________________________________
```

**Correct antwoord:**
```
Manier 1: PHPMyAdmin GUI
Manier 2: php bin/console doctrine:database:create
```

---

## SECTION 10: ERROR FIXING

### Q10.1: Welke Fout?
```
Error: "No route found for GET /product/"
```

**Hoe fix je dit?**

**Jouw antwoord:**
```
_________________________________
```

**Correct antwoord:**
```
Check: Is route geregistreerd? 
       Klopt de URL?
       Klopt de HTTP method (GET)?
```

### Q10.2: Database Error
```
Error: "Column 'category_id' not found in table 'products'"
```

**Hoe fix je dit?**

**Jouw antwoord:**
```
_________________________________
```

**Correct antwoord:**
```
Migratie niet gedraaid!
php bin/console doctrine:migrations:migrate
```

---

## SCORING

Hoeveel heb je goed?

- **80-100%** ✅ Je bent ready! Go examen!
- **60-80%** ⚠️ Refresh je geheugen
- **40-60%** 📚 Nog even STUDY_GUIDE lezen
- **0-40%** 🚨 Veel oefenen nodig!

---

## RECAP: TOP 10 DINGEN TE ONTHOUDEN

1. Entity = database tabel in PHP
2. ManyToOne = veel naar 1
3. OneToMany = 1 naar veel
4. Migratie = database versioning
5. Repository = database queries
6. Controller = routes + logica
7. Form = validatie + HTML
8. Template = Twig + HTML weergave
9. EntityManager = persist + flush
10. CSRF token = veiligheid delete

---

Veel sterkte! 🍀
