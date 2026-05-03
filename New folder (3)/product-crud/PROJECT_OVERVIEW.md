# 📚 PROJECT OVERZICHT - Wat is Waar?

Dit bestand helpt je navigeren door het project.

---

## 📖 DOCUMENTATIE (LEES DEZE EERST!)

| Bestand | Doel | Lees | Tijd |
|---------|------|------|------|
| **README.md** | Volledige project uitleg | ✅ EERST | 10 min |
| **QUICKSTART.txt** | Snel beginnen (stap 1-6) | ✅ SECOND | 5 min |
| **STUDY_GUIDE.md** | Diep uitleg alle concepten | ✅ BEFORE EXAM | 30 min |
| **CHEAT_SHEET.md** | Code templates & snippets | 📋 ALS REFERENCE | 5 min |
| **HOW_TO_BUILD_CRUD.md** | Zelf CRUD bouwen stap/stap | 💪 PRACTICE | 20 min |

---

## 🗂️ PROJECT STRUCTUUR

### `/src/Entity/` - DATABASE MODELS
```
Category.php    ← OneToMany: 1 categorie → veel producten
Product.php     ← ManyToOne: veel producten → 1 categorie
```
**LEREN:** Attributes, relaties, getters/setters

### `/src/Form/` - FORMULIEREN
```
CategoryType.php  ← Form voor categorie (CREATE/UPDATE)
ProductType.php   ← Form voor product (CREATE/UPDATE)
```
**LEREN:** Form builder, field types, validation

### `/src/Controller/` - ROUTES & LOGICA
```
CategoryController.php  ← 5 routes: C R R U D
ProductController.php   ← 5 routes: C R R U D
```
**LEREN:** Routes, dependency injection, EntityManager

### `/src/Repository/` - DATABASE QUERIES
```
CategoryRepository.php  ← Custom SELECT queries
ProductRepository.php   ← Custom SELECT queries
```
**LEREN:** QueryBuilder, custom query methods

### `/templates/` - HTML VIEWS
```
base.html.twig              ← Base layout (navbar, flashes)
index.html.twig             ← Home pagina

product/
  ├── index.html.twig       ← Master: alle producten
  ├── show.html.twig        ← Detail: 1 product
  └── form.html.twig        ← Form: create/edit

category/
  ├── index.html.twig       ← Master: alle categorieën
  ├── show.html.twig        ← Detail: 1 categorie
  └── form.html.twig        ← Form: create/edit
```
**LEREN:** Twig syntax, loops, conditionals, forms

### `/migrations/` - DATABASE VERSIONING
```
Version20260503000001.php  ← Create categories tabel
Version20260503000002.php  ← Create products tabel + FK
```
**LEREN:** SQL creation, foreign keys, migrations

### `/config/` - CONFIGURATIE
```
packages/
  ├── doctrine.yaml        ← Database setup
  ├── framework.yaml       ← Framework config
  ├── twig.yaml            ← Template engine
  ├── form.yaml            ← Form config
  └── doctrine_migrations.yaml
  
services.yaml              ← Dependency injection
routes.yaml                ← Route registration
```
**LEREN:** YAML syntax, configuration

### `/public/` - WEB ROOT
```
index.php  ← Entry point (Symfony front controller)
```

### ROOT LEVEL
```
.env                  ← Environment variables (DATABASE_URL)
.env.local           ← Je LOKALE settings (niet in git!)
composer.json        ← Dependencies list
```

---

## 🎓 LEER PAD - IN DEZE VOLGORDE

### DAG 1: BASIS KENNIS
```
1. Lees README.md → Begrijp het project
2. Lees QUICKSTART.txt → Setup en start
3. Installeer en draai project
4. Bekijk homepage en navigeer rond
```

### DAG 2: CONCEPTEN LEREN
```
1. STUDY_GUIDE.md → Lees alle concepten
   - Entities
   - Relaties
   - Migraties
   - Repositories
   - Controllers
   - Forms
   - Templates

2. Per concept:
   - Lees de theorie
   - Kijk naar voorbeeld in project
   - Voeg logging/comments toe
```

### DAG 3: PRACTICE CODING
```
1. HOW_TO_BUILD_CRUD.md → Volg stap voor stap
2. Bouw eigen CRUD voor "Comments"
3. Test alles zelf
4. Zorg database wijzigingen via migraties
```

### DAG 4: EXAMEN VOORBEREIDING
```
1. CHEAT_SHEET.md → Print of open
2. Maak snelle codebase
3. Test alle CRUD operaties
4. Zorg met snelheid
```

---

## 🔍 QUICK FILE LOOKUP

### "Hoe maak ik een Entity?"
→ Zie: `src/Entity/Category.php` + `STUDY_GUIDE.md` § 1

### "Hoe maak ik een Relatie?"
→ Zie: `src/Entity/Product.php` (ManyToOne) + `src/Entity/Category.php` (OneToMany) + `STUDY_GUIDE.md` § 2

### "Hoe maak ik een Migratie?"
→ Zie: `migrations/Version20260503*.php` + `STUDY_GUIDE.md` § 3

### "Hoe maak ik een Repository query?"
→ Zie: `src/Repository/ProductRepository.php` + `STUDY_GUIDE.md` § 4

### "Hoe maak ik een Controller route?"
→ Zie: `src/Controller/ProductController.php` + `STUDY_GUIDE.md` § 5

### "Hoe maak ik een Form?"
→ Zie: `src/Form/ProductType.php` + `STUDY_GUIDE.md` § 6

### "Hoe maak ik een Template?"
→ Zie: `templates/product/*.twig` + `STUDY_GUIDE.md` § 7

---

## 💡 LEER TIPS

### Voor elke file, vraag jezelf:

1. **DOEL** - Wat doet dit bestand?
2. **INPUT** - Wat gaat erin?
3. **OUTPUT** - Wat komt eruit?
4. **WAAROM** - Waarom doen we dit?

### Voorbeeld: ProductController

```
1. DOEL: Verwerk product requests (routes)
2. INPUT: HTTP request naar /product/5/edit
3. OUTPUT: HTML response met formulier
4. WAAROM: Connects database → browser
```

---

## 🧪 TESTING - Hoe test je?

### CREATE (toevoegen)
1. Ga naar: `http://localhost:8000/product/create/new`
2. Vul formulier in
3. Klik "Opslaan"
4. Check: product in database? In lijst?

### READ (lezen)
1. Ga naar: `http://localhost:8000/product/`
2. Bekijk: alle producten zichtbaar?
3. Klik: op product detail link
4. Check: product details correct?

### UPDATE (wijzigen)
1. Ga naar: `http://localhost:8000/product/5/edit`
2. Wijzig: een veld (bv. prijs)
3. Klik: "Opslaan"
4. Check: wijziging in database?

### DELETE (verwijderen)
1. Ga naar: `http://localhost:8000/product/`
2. Klik: delete button
3. Confirm: "Weet je zeker?"
4. Check: product weg uit database?

---

## 🐛 DEBUGGEN

### Debug Tools

```bash
# Alle routes tonen
php bin/console debug:router

# Services/dependencies tonen
php bin/console debug:autowiring

# Database query logger
// In controller:
dd($em->getRepository(Product::class)->findAll());

# Twig dump
{{ dump(product) }}

# Error log
tail -f var/log/dev.log
```

---

## 📋 EXAMEN CHECKLIST

Voor je examen zorg voor:

- ✅ Entities gemaakt met relaties
- ✅ Migraties aangemaakt en gedraaid
- ✅ Repositories met queries
- ✅ Controllers met alle CRUD
- ✅ Forms voor validatie
- ✅ Templates voor weergave
- ✅ Database ingericht
- ✅ Routes werkend
- ✅ CSRF tokens in delete forms
- ✅ Flash messages voor feedback

---

## 🎯 HOOFD DOELSTELLINGEN

### Je moet kunnen:
1. ✅ Entity aanmaken met Doctrine attributes
2. ✅ Migratie genereren en uitvoeren
3. ✅ OneToMany/ManyToOne relatie opzetten
4. ✅ Repository queries schrijven
5. ✅ Controller routes definiëren
6. ✅ Form fields toevoegen en valideren
7. ✅ Template weergeven met Twig
8. ✅ CREATE/READ/UPDATE/DELETE operaties
9. ✅ Database data persisteren
10. ✅ Database data manipuleren

---

## 📞 EXTRA HULP

### Fout: "Entity not mapped"
→ Check: Entity in `/src/Entity/` folder?

### Fout: "No route found"
→ Check: `path()` naam klopt? Route geregistreerd?

### Fout: "CSRF token invalid"
→ Check: Token in form? `csrf_token()` helper?

### Fout: "Database error"
→ Check: DATABASE_URL in .env? MySQL draait?

### Fout: "Column not found"
→ Check: Migratie gedraaid? `doctrine:migrations:migrate`?

---

Veel sterkte! Je bent ready! 🚀
