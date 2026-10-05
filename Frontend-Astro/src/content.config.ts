import { defineCollection, z } from "astro:content";
import { glob } from "astro/loaders";

/**
 * The menu categories, in the order they appear on the page.
 *
 * SINGLE SOURCE. The schema below validates against it and both menu
 * surfaces (MenuList.astro and pages/menu/index.astro) import it. Each of
 * those used to keep its own copy of the list, undocumented: adding a
 * category to the schema left the build green and the item invisible.
 *
 * To add a category: add it here, then add its label under "jadwal.categories"
 * in every src/i18n/*.json file. Nothing else.
 */

/**
 * The tag vocabulary for menu items.
 *
 * A free-form string list let a typo through in silence: the badge rendered
 * from whatever was written, and the schema layer quietly dropped anything
 * it did not recognize. An enum turns that into a build error naming the
 * file and the allowed values.
 *
 * Which of these reach structured data is decided in core (DIET_MAP), not
 * here: diet tags become suitableForDiet, the rest are page-only labels.
 * To add a tag: add it here, then add its label under "jadwal.tags" in every
 * src/i18n/*.json file.
 */
export const MENU_TAGS = ["wajib", "sunnah", "jumat", "terjemah", "qiyamullail"] as const;

export const MENU_CATEGORIES = ["shalat", "kegiatan", "pengumuman", "donasi"] as const;


/**
 * menu — the informasi collection, grouped by category.
 * Informasi masjid kecil dan rotatif: tandai item `seasonal: true` dan
 * ini akan mendapat tanda di daftar. Menambah entri adalah menambah file .md.
 */
const menu = defineCollection({
  loader: glob({ pattern: "**/*.md", base: "./src/content/menu" }),
  schema: z.object({
    name: z.string(),
    description: z.string().default(""),
    price: z.number().nonnegative(),
    currency: z.string().default("IDR"),
    /** Waktu shalat (contoh "04:30"), ditampilkan alih-alih harga pada item gratis */
    time: z.string().optional(),
    /** To add a category, extend this enum and add its label under
        "jadwal.categories" in each src/i18n/*.json file. */
    category: z.enum(MENU_CATEGORIES),
    /** wajib / sunnah / jumat / terjemah / qiyamullail */
    tags: z.array(z.enum(MENU_TAGS)).default([]),
    /** Seasonal badge — bagian yang pengunjung selalu periksa */
    seasonal: z.boolean().default(false),
    order: z.number().int().default(100),
    available: z.boolean().default(true),
    featured: z.boolean().default(false),
  }),
});

/**
 * locations — the masjid collection. Multiple locations are supported from
 * the start rather than retrofitted. With one file the site reads as a
 * single masjid; add a second and the home page grows an "all locations"
 * link and the contact page becomes a grid of cards. No code changes.
 */
const locations = defineCollection({
  loader: glob({ pattern: "**/*.md", base: "./src/content/locations" }),
  schema: z.object({
    name: z.string(),
    streetAddress: z.string(),
    addressLocality: z.string(),
    addressRegion: z.string(),
    postalCode: z.string(),
    addressCountry: z.string().default("ID"),
    phone: z.string().optional(),
    /** Map link — where the "Get directions" button goes */
    mapUrl: z.string().url().optional(),
    /** Weekly hours. schema.org openingHoursSpecification is built from this. */
    hours: z
      .array(
        z.object({
          dayOfWeek: z.array(z.string()),
          opens: z.string(),
          closes: z.string(),
        })
      )
      .default([]),
    /** The "fasilitas jamaah" facts. An honest no is still an answer. */
    wifi: z.boolean().default(true),
    outlets: z.boolean().default(true),
    laptopFriendly: z.boolean().default(true),
    /** The main masjid: the home page card and the schema.org entry use it */
    primary: z.boolean().default(false),
    order: z.number().int().default(100),
  }),
});

/**
 * faq — the questions collection. Wired into FAQPage schema automatically
 * and written out to /llms.txt, so a question answered here is a question an
 * answer engine can quote. This is the cheapest structured data on the site.
 */
const faq = defineCollection({
  loader: glob({ pattern: "**/*.md", base: "./src/content/faq" }),
  schema: z.object({
    question: z.string(),
    answer: z.string(),
    order: z.number().int().default(100),
  }),
});

/**
 * legal — the privacy and terms pages. The body is markdown and is meant to
 * be replaced with your own text; the shipped copy is a starting structure,
 * not legal advice. See docs/legal-templates.md before publishing it.
 */
const legal = defineCollection({
  loader: glob({ pattern: "**/*.md", base: "./src/content/legal" }),
  schema: z.object({
    title: z.string(),
    /** One sentence about what this page covers. It becomes the meta
     *  description; without it the description repeated the title. */
    description: z.string(),
    updated: z.string(),
  }),
});

export const collections = { menu, locations, faq, legal };
