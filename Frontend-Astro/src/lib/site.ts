/**
 * Site configuration, written to be the only file you have to edit.
 * Interface strings live in src/i18n/*.json, and addresses and hours live in
 * the src/content/locations/ collection. What is left here is the identity
 * of the business itself.
 */
import { createT, DEFAULT_LOCALE, type Locale } from "@studio/core/utils/i18n";
import id from "../i18n/id.json";

/** Active language. Set it to "id" and the whole interface switches. */
export const LOCALE: Locale = "id";

const dicts: Record<Locale, typeof id> = { id };
export const t = createT(dicts[LOCALE], id);

/**
 * Where the contact form posts. Leave it "#" if you handle posts through
 * your own Laravel API endpoint instead.
 */
export const contactFormAction = "#";

/**
 * The map on the contact page.
 *
 * Paste the `src` of your provider's embed iframe here (in Google Maps:
 * Share -> Embed a map -> copy the src). Leave it empty and the section does
 * not render at all.
 *
 * The shipped Content-Security-Policy has `frame-src 'none'`, which blocks
 * every embed. When you set this, open public/_headers and replace `'none'`
 * in `frame-src` with the host of the address you pasted.
 */
export const mapEmbedUrl = "";

/**
 * The identity of the masjid, feeding the schema.org LocalBusiness
 * fields. Addresses and hours come from the location files instead.
 */
export const masjid = {
  name: t("site.name"),
  email: "info@example.com",
  phone: "+62 21 555 0142",
  priceRange: "$",
  servesCuisine: [],
  /** Year founded — used on the about page and in schema.org */
  foundingYear: "2020",
};

/* The demo's contact details are placeholders. Unlike example.com they look
   real to anyone who does not know those conventions, and they are declared
   in three JSON-LD nodes and in /llms.txt. */
const DEMO_CONTACT = {
  email: "info@example.com",
  phone: "+62 21 555 0142",
};

if (import.meta.env.PROD) {
  const stale = (Object.keys(DEMO_CONTACT) as Array<keyof typeof DEMO_CONTACT>).filter(
    (key) => masjid[key] === DEMO_CONTACT[key]
  );
  if (stale.length > 0) {
    console.warn(
      `\n  [masjid] Still the demo's ${stale.join(" and ")} in src/lib/site.ts.\n` +
        `           These are published in structured data and in /llms.txt.\n`
    );
  }
}
