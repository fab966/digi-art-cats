![Joomla 4 & 5](https://img.shields.io/badge/Joomla-4%20%7C%205-green?logo=joomla)
![License GPL 2+](https://img.shields.io/badge/License-GPL%202%2B-blue)
![PHP 8+](https://img.shields.io/badge/PHP-8%2B-blue?logo=php)
![Latest Release](https://img.shields.io/github/v/release/fab966/digi-art-cats?label=Version&color=orange)
![Stable Version](https://img.shields.io/badge/Version-Stable-yellow)

# Digi ArtCats Module for Joomla

**Digi ArtCats** is a flexible Joomla 4/5 module that dynamically displays categories and articles from the `com_content` component. Designed for both static and dynamic contexts, it supports nested categories, article listings, and customizable layouts.

## 🚀 Features

- Display categories and/or articles based on current page context
- Dynamic mode: adapts to the current category or article view
- Static mode: fixed parent category display
- Optional article listing with date formatting
- Customizable heading levels and layout overrides
- Multilingual and ACL-aware
- Joomla 5 compatible (PSR-4 autoloading, PHP 8.4 ready)

## 📦 Installation

1. Download or clone the repository into your Joomla `/modules/` directory:
   ```bash
   git clone https://github.com/yourusername/mod_digi_artcats.git
   ```
2. Install via Joomla Extension Manager or manually place the folder.
3. Enable the module and assign it to a position and menu item.

## 🧩 Folder Structure

```
mod_digi_artcats/
├── mod_digi_artcats.php          # Entry point
├── mod_digi_artcats.xml          # Manifest file
├── src/
│   └── Helper/
│       └── DigiArtCatsHelper.php # Business logic
├── tmpl/
│   ├── default.php               # Main layout
│   └── default_items.php         # Item rendering
├── language/
│   └── en-GB/
│       └── mod_digi_artcats.ini  # Language strings
├── index.html                    # Security file
```

## 🛠 Configuration Parameters

| Parameter                  | Description                                  |
|---------------------------|----------------------------------------------|
| Mode                      | `normal` or `dynamic`                        |
| Parent Category           | ID of the root category                      |
| Show Articles             | Include articles in the listing             |
| Show Empty Categories     | Display categories with no articles         |
| Max Level                 | Depth of category tree                      |
| Article Ordering          | Sort articles by date, title, etc.          |
| Heading Level             | HTML heading tag level (e.g. h3, h4)         |
| Show Date                 | Display article creation or publish date     |

## 📚 Compatibility

- Joomla 4.x and Joomla 5.x
- PHP 8.1+
- Fully PSR-12 compliant

## 🤝 Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you’d like to change.

## 📄 License

GNU General Public License v2 or later. See the [LICENSE.txt](LICENSE.txt) file for details.

## 🙌 Credits

Developed by [Fab](https://github.com/fab966)  
Inspired by Joomla’s native `mod_articles_categories` module and from ArtCats25 Module of Omar E. Ramos
