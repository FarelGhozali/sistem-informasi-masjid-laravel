/**
 * api.ts — the one place the frontend talks to the Laravel API.
 *
 * The site is static: it is built once and served as files. Finance numbers
 * cannot be read at build time, because the treasurer records a transaction
 * after the build and the page would show the balance from whenever the site
 * was last deployed. The browser asks the API instead, and this module keeps
 * the base address and the response shapes in a single place so a page never
 * builds a URL by hand.
 *
 * PUBLIC_API_BASE_URL is set in .env (see .env.example). The PUBLIC_ prefix
 * is what makes it readable from the browser; the fallback is the address
 * `php artisan serve` uses in development. In production it points at the
 * Laravel subdomain or the /api folder on the same host.
 */

const BASE_URL = (import.meta.env.PUBLIC_API_BASE_URL ?? "http://localhost:8000/api").replace(
  /\/+$/,
  ""
);

/** Every API response is wrapped: { status: "success", data: ... }. */
interface ApiEnvelope<T> {
  status: string;
  data: T;
}

export interface KeuanganRingkasan {
  total_pemasukan: number;
  total_pengeluaran: number;
  saldo_kas: number;
  total_pemasukan_formatted: string;
  total_pengeluaran_formatted: string;
  saldo_kas_formatted: string;
}

export type JenisTransaksi = "pemasukan" | "pengeluaran";

export interface TransaksiKeuangan {
  id: number;
  jenis: JenisTransaksi;
  judul: string;
  /** The API sends the `decimal:2` cast as a string ("3850000.00"). */
  nominal: string;
  /** An ISO timestamp; only the date part is meaningful. */
  tanggal: string;
  keterangan: string | null;
  /** When the treasurer last touched the row — the fairest "terakhir diperbarui". */
  updated_at: string;
  kategori: { id: number; nama: string; jenis: JenisTransaksi } | null;
}

/** Laravel's `paginate()` payload. */
export interface Paginated<T> {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  data: T[];
}

async function apiGet<T>(path: string, params?: Record<string, string>): Promise<T> {
  const query = params && Object.keys(params).length > 0 ? `?${new URLSearchParams(params)}` : "";
  const res = await fetch(`${BASE_URL}${path}${query}`, {
    headers: { Accept: "application/json" },
  });
  if (!res.ok) {
    throw new Error(`API ${res.status} ${res.statusText} — ${path}`);
  }
  return (await res.json()) as T;
}

/** Totals across every transaction, for the three summary blocks. */
export async function getKeuanganRingkasan(): Promise<KeuanganRingkasan> {
  const res = await apiGet<ApiEnvelope<KeuanganRingkasan>>("/v1/keuangan/ringkasan");
  return res.data;
}

/** One page of the ledger, newest first, optionally filtered by kind. */
export async function getKeuanganTransaksi(
  opts: { page?: number; jenis?: JenisTransaksi | ""; limit?: number } = {}
): Promise<Paginated<TransaksiKeuangan>> {
  const params: Record<string, string> = {};
  /* Page 1 is the default; sending it would only lengthen the URL. */
  if (opts.page && opts.page > 1) params.page = String(opts.page);
  if (opts.jenis) params.jenis = opts.jenis;
  if (opts.limit) params.limit = String(opts.limit);
  const res = await apiGet<ApiEnvelope<Paginated<TransaksiKeuangan>>>(
    "/v1/keuangan/transaksi",
    params
  );
  return res.data;
}

/**
 * The whole ledger, in one array.
 *
 * The keuangan page draws per-month bars, per-category shares and a searchable
 * table — all of which need every row, not one page of them. The API's `limit`
 * parameter keeps that to a couple of requests. MAX_PAGES is a seatbelt: at
 * 100 rows a page that is 5.000 transactions, far past what a browser tab
 * (or a mosque's bookkeeping) should aggregate, and `truncated` tells the
 * page to say so rather than to pretend it saw everything.
 */
export const ALL_TRANSAKSI_PAGE_LIMIT = 100;
export const ALL_TRANSAKSI_MAX_PAGES = 50;

export async function getAllKeuanganTransaksi(): Promise<{
  data: TransaksiKeuangan[];
  total: number;
  truncated: boolean;
}> {
  const first = await getKeuanganTransaksi({ page: 1, limit: ALL_TRANSAKSI_PAGE_LIMIT });
  const pages = Math.min(first.last_page, ALL_TRANSAKSI_MAX_PAGES);
  const rest = await Promise.all(
    Array.from({ length: pages - 1 }, (_, i) =>
      getKeuanganTransaksi({ page: i + 2, limit: ALL_TRANSAKSI_PAGE_LIMIT })
    )
  );
  return {
    data: [first, ...rest].flatMap((res) => res.data),
    total: first.total,
    truncated: first.last_page > pages,
  };
}
