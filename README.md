# 🛡️ PHP Anonymizer

**PHP Anonymizer** is a modular library for detecting and replacing sensitive data (names, emails, phone numbers, etc.) in free text. It is ideal for anonymizing user data before storing or processing it—e.g., for GDPR compliance.

---

## 🚀 Features

- 🔍 Detects and anonymizes:
  - Names
  - Surnames
  - Emails
  - Phone numbers
  - Street addresses
  - Numbers
  - Dates
- 🧱 Modular structure (handlers for each data type)
- 📁 Supports CSV-based dictionaries (e.g., list of names)
- 🧪 Fully tested with PHPUnit
- ⚡ Lightweight, dependency-free (except for Composer)

---

## 📦 Installation

1. Clone the repository and install dependencies via Composer:

```bash
git clone https://github.com/your-username/anonymizer.git
cd anonymizer
composer install
```

2. Run demo or tests (optional)

---

## 🧑‍💻 Usage Example

```php
require_once 'vendor/autoload.php';

use App\Anonymizer;

$anonymizer = new Anonymizer();

$text = "Pan Novák žije na ulici Nová 34, Brno";
$result = $anonymizer->anonymize($text, ['SURNAME', 'ADDRESS'], '{_XXX_}');

echo $result;
// Output: Pan {_XXX_} žije na ulici {_XXX_}, {_XXX_}
```

---

## ⚙️ Anonymization Keys

When calling `anonymize($text, $settings)` you can specify which data types to anonymize:

| Key        | Description            |
|------------|------------------------|
| `NAME`     | First names            |
| `SURNAME`  | Last names             |
| `EMAIL`    | Email addresses        |
| `TEL`      | Phone numbers          |
| `ADDRESS`  | Street addresses       |
| `NUMBERS`  | General numbers (IDS, CARD NUMBERS, ...)       |
| `DATES`    | Calendar dates         |

---

## 🧪 Running Tests

This project uses **PHPUnit** for unit testing. To run the tests:

```bash
vendor/bin/phpunit
```

All handlers are tested individually to ensure reliability.

---

## 📁 Project Structure

```
anonymizer/
├── src/
│   ├── Anonymizer.php
│   ├── handlers/
│   └── loaders/
├── tests/
│   └── HandlerTests.php
├── example/
│   └── demo.php
└── composer.json
```

---

## 📌 Roadmap

- [ ] Add support for national ID numbers
- [ ] Export results in JSON
- [ ] Build web interface or REST API
- [ ] Language-specific customization

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).

---

## 🙋 Author

Created by **PetrKuba-dev**

- [GitHub Profile](https://github.com/PetrKuba-dev)
- [LinkedIn or Website] *(optional)*

---

## 🌍 Want to translate for your language?

You can provide your own dictionary files (e.g., list of names) and adjust regex rules inside each handler to suit your locale. Feel free to fork and customize!
