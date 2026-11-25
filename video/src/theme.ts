import type {CSSProperties} from 'react';

/* Warna sama dengan assets/css/app.css pada aplikasi. */
export const C = {
  ink: '#1B2540',
  ink2: '#55607A',
  kertas: '#F3F6FC',
  putih: '#FFFFFF',
  biru: '#2F6BFF',
  biruMuda: '#DCE7FF',
  mint: '#C6F1E0',
  mintTua: '#0B5E43',
  sun: '#FFE08A',
  koral: '#FFD3CB',
  koralTua: '#8F2A1B',
} as const;

export const FONT =
  'ui-rounded, "Segoe UI", system-ui, -apple-system, "Helvetica Neue", Arial, sans-serif';

/* Kotak bergaya "stiker": garis tinta tebal + bayangan datar bergeser. */
export const kotak = (bg: string = C.putih): CSSProperties => ({
  background: bg,
  border: `4px solid ${C.ink}`,
  borderRadius: 24,
  boxShadow: `8px 8px 0 ${C.ink}`,
});
