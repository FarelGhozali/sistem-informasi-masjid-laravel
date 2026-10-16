/**
 * /sitemap.xml — every page this site has.
 *
 * Written here rather than pulled in as an integration. The map is a short
 * list of static routes; a dependency to produce that is a dependency the
 * buyer has to keep updated, and this file is short enough to read in one go.
 *
 * The list lives in this file so it travels with the theme. Add a static page
 * and add its path here; nothing else needs to know about it.
 */
import type { APIRoute } from "astro";
import { getCollection } from "astro:content";
import { sitemapXml, xmlResponse } from "@studio/core/utils/crawl";
import { getAllKegiatan } from "../lib/api";

const PATHS = [
  { path: "/" },
  { path: "/jadwal/" },
  { path: "/keuangan/" },
  { path: "/about/" },
  { path: "/contact/" },
];

export const GET: APIRoute = async ({ site }) => {
  /* The legal pages are the only ones that record when they changed, in the
     `updated` field of their frontmatter — and they are also the pages where
     the date matters, because a privacy policy is read against the day it was
     written. The rest carry no lastmod rather than a guessed one. */
  const legal = (await getCollection("legal")).sort((a, b) => a.id.localeCompare(b.id));

  /* The agenda's detail pages are generated at build time from the same API
     call, so the map lists exactly the pages that exist. If the API is down
     while building, the map simply omits them rather than guessing. */
  let kegiatan: Array<{ path: string; lastmod: string }> = [];
  try {
    kegiatan = (await getAllKegiatan()).data.map((k) => ({
      path: `/jadwal/${encodeURIComponent(k.slug)}/`,
      lastmod: k.updated_at.slice(0, 10),
    }));
  } catch {
    kegiatan = [];
  }

  const paths = [
    ...PATHS,
    ...legal.map((entry) => ({ path: `/legal/${entry.id}/`, lastmod: entry.data.updated })),
    ...kegiatan,
  ];
  return xmlResponse(sitemapXml(site, paths));
};
