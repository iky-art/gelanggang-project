import React from 'react';
import {AbsoluteFill, Series, interpolate, spring, useCurrentFrame, useVideoConfig} from 'remotion';
import {C, FONT, kotak} from './theme';

/* Durasi tiap adegan (frame, 30 fps). Total 1275 frame = 42,5 detik. */
export const DURASI = {
  judul: 105,
  masalah: 150,
  fitur: 135,
  alur: 135,
  klasemen: 180,
  bagan: 180,
  skor: 150,
  aman: 120,
  penutup: 120,
} as const;
export const TOTAL: number = Object.values(DURASI).reduce((a, b) => a + b, 0);

const klem = {extrapolateLeft: 'clamp', extrapolateRight: 'clamp'} as const;

/* Animasi muncul memantul. Dipanggil di dalam komponen (bukan di dalam loop). */
const usePop = (delay: number): number => {
  const frame = useCurrentFrame();
  const {fps} = useVideoConfig();
  return spring({frame: Math.max(0, frame - delay), fps, config: {damping: 14, stiffness: 120}});
};

/* ---------- Bagian pakai ulang ---------- */

const Scene: React.FC<{durasi: number; children: React.ReactNode}> = ({durasi, children}) => {
  const frame = useCurrentFrame();
  const opacity = interpolate(frame, [0, 8, durasi - 8, durasi], [0, 1, 1, 0], klem);
  return (
    <AbsoluteFill style={{background: C.kertas, fontFamily: FONT, color: C.ink, opacity}}>
      {children}
    </AbsoluteFill>
  );
};

const Kepala: React.FC<{kicker: string; judul: string}> = ({kicker, judul}) => {
  const p = usePop(0);
  return (
    <div style={{position: 'absolute', top: 70, left: 110, right: 110, opacity: p, transform: `translateY(${(1 - p) * 24}px)`}}>
      <div
        style={{
          display: 'inline-block',
          background: C.mint,
          color: C.mintTua,
          border: `4px solid ${C.ink}`,
          borderRadius: 999,
          padding: '6px 26px',
          fontSize: 30,
          fontWeight: 800,
        }}
      >
        {kicker}
      </div>
      <div style={{fontSize: 76, fontWeight: 800, letterSpacing: -1.5, marginTop: 18, lineHeight: 1.1}}>{judul}</div>
    </div>
  );
};

const Chip: React.FC<{teks: string; bg?: string; ukuran?: number}> = ({teks, bg = C.putih, ukuran = 34}) => (
  <span
    style={{
      display: 'inline-block',
      background: bg,
      border: `4px solid ${C.ink}`,
      borderRadius: 999,
      padding: '8px 28px',
      fontSize: ukuran,
      fontWeight: 800,
    }}
  >
    {teks}
  </span>
);

const Nomor: React.FC<{n: number}> = ({n}) => (
  <span
    style={{
      flex: 'none',
      width: 64,
      height: 64,
      borderRadius: '50%',
      background: C.ink,
      color: C.putih,
      fontSize: 36,
      fontWeight: 800,
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
    }}
  >
    {n}
  </span>
);

const Catatan: React.FC<{teks: string}> = ({teks}) => (
  <div style={{position: 'absolute', bottom: 40, right: 110, fontSize: 26, color: C.ink2, fontWeight: 700}}>{teks}</div>
);

/* ---------- 1. Judul ---------- */

const SceneJudul: React.FC<{durasi: number}> = ({durasi}) => {
  const logo = usePop(0);
  const nama = usePop(12);
  const sub = usePop(26);
  const tag = usePop(40);
  return (
    <Scene durasi={durasi}>
      <AbsoluteFill style={{justifyContent: 'center', alignItems: 'center', gap: 30}}>
        <div
          style={{
            width: 180,
            height: 180,
            borderRadius: 44,
            background: C.biru,
            border: `6px solid ${C.ink}`,
            boxShadow: `10px 10px 0 ${C.ink}`,
            position: 'relative',
            transform: `scale(${logo})`,
          }}
        >
          <div style={{position: 'absolute', inset: 34, border: `18px solid ${C.sun}`, borderRadius: '50%'}} />
        </div>
        <div style={{fontSize: 160, fontWeight: 800, letterSpacing: -4, opacity: nama, transform: `translateY(${(1 - nama) * 40}px)`}}>
          Gelanggang
        </div>
        <div style={{fontSize: 50, color: C.ink2, fontWeight: 700, opacity: sub, transform: `translateY(${(1 - sub) * 30}px)`}}>
          Manajemen turnamen e-sport sekolah
        </div>
        <div style={{display: 'flex', gap: 18, opacity: tag, transform: `translateY(${(1 - tag) * 30}px)`}}>
          <Chip teks="PHP" bg={C.biruMuda} />
          <Chip teks="MySQL" bg={C.mint} />
          <Chip teks="Bootstrap" bg={C.sun} />
          <Chip teks="Berjalan offline" bg={C.koral} />
        </div>
      </AbsoluteFill>
    </Scene>
  );
};

/* ---------- 2. Masalah ---------- */

const MASALAH = [
  'Klasemen dihitung manual, sering keliru saat poin sama',
  'Bagan digambar tangan, sulit diperbarui',
  'Jadwal bentrok: tim atau arena yang sama',
  'Skor berubah tanpa jejak',
];

const BarisMasalah: React.FC<{teks: string; i: number}> = ({teks, i}) => {
  const p = usePop(14 + i * 20);
  return (
    <div
      style={{
        ...kotak(C.koral),
        padding: '24px 40px',
        fontSize: 46,
        fontWeight: 700,
        display: 'flex',
        alignItems: 'center',
        gap: 28,
        opacity: p,
        transform: `translateX(${(1 - p) * -90}px)`,
      }}
    >
      <Nomor n={i + 1} />
      {teks}
    </div>
  );
};

const SceneMasalah: React.FC<{durasi: number}> = ({durasi}) => {
  const solusi = usePop(95);
  return (
    <Scene durasi={durasi}>
      <Kepala kicker="Masalahnya" judul="Turnamen antar kelas, dicatat manual" />
      <div style={{position: 'absolute', top: 300, left: 110, right: 110, display: 'flex', flexDirection: 'column', gap: 26}}>
        {MASALAH.map((t, i) => (
          <BarisMasalah key={t} teks={t} i={i} />
        ))}
      </div>
      <div
        style={{
          ...kotak(C.mint),
          position: 'absolute',
          left: 110,
          right: 110,
          bottom: 70,
          padding: '22px 40px',
          fontSize: 44,
          fontWeight: 800,
          textAlign: 'center',
          opacity: solusi,
          transform: `scale(${0.9 + solusi * 0.1})`,
        }}
      >
        Gelanggang menghitung, menjadwalkan, dan mencatat otomatis
      </div>
    </Scene>
  );
};

/* ---------- 3. Fitur ---------- */

const FITUR: ReadonlyArray<readonly [string, string, string]> = [
  ['3 format turnamen', 'Grup + gugur, liga, atau gugur', C.biruMuda],
  ['Jadwal otomatis', 'Metode lingkaran, waktu, dan arena', C.mint],
  ['Klasemen otomatis', 'Poin, selisih, head-to-head', C.sun],
  ['Bagan gugur', 'Bye, seed dari grup, juara 3', C.koral],
  ['Skor aman', 'Transaksi, kunci, koreksi + log', C.biruMuda],
  ['Anti bentrok', 'Tim dan arena tidak ganda', C.mint],
];

const KartuFitur: React.FC<{judul: string; isi: string; bg: string; i: number}> = ({judul, isi, bg, i}) => {
  const p = usePop(12 + i * 8);
  return (
    <div style={{...kotak(bg), padding: '30px 34px', opacity: p, transform: `translateY(${(1 - p) * 50}px) scale(${0.92 + p * 0.08})`}}>
      <div style={{fontSize: 50, fontWeight: 800, lineHeight: 1.15}}>{judul}</div>
      <div style={{fontSize: 32, fontWeight: 600, marginTop: 12, color: C.ink2}}>{isi}</div>
    </div>
  );
};

const SceneFitur: React.FC<{durasi: number}> = ({durasi}) => (
  <Scene durasi={durasi}>
    <Kepala kicker="Fitur" judul="Semua yang dibutuhkan panitia" />
    <div
      style={{
        position: 'absolute',
        top: 300,
        left: 110,
        right: 110,
        display: 'grid',
        gridTemplateColumns: 'repeat(3, 1fr)',
        gap: 36,
      }}
    >
      {FITUR.map(([judul, isi, bg], i) => (
        <KartuFitur key={judul} judul={judul} isi={isi} bg={bg} i={i} />
      ))}
    </div>
  </Scene>
);

/* ---------- 4. Alur ---------- */

const LANGKAH = ['Buat turnamen', 'Daftar tim', 'Mulai & jadwal', 'Isi skor', 'Babak gugur', 'Juara'];

const SceneAlur: React.FC<{durasi: number}> = ({durasi}) => {
  const frame = useCurrentFrame();
  const aktif = Math.floor((frame - 18) / 16);
  return (
    <Scene durasi={durasi}>
      <Kepala kicker="Alur" judul="Dari pendaftaran sampai juara" />
      <div style={{position: 'absolute', top: 380, left: 70, right: 70, display: 'flex', alignItems: 'center', justifyContent: 'center', gap: 14}}>
        {LANGKAH.map((t, i) => {
          const nyala = i <= aktif;
          return (
            <React.Fragment key={t}>
              <div
                style={{
                  ...kotak(nyala ? C.mint : C.putih),
                  boxShadow: nyala ? `8px 8px 0 ${C.ink}` : `4px 4px 0 ${C.ink}`,
                  padding: '28px 18px',
                  width: 250,
                  textAlign: 'center',
                  fontSize: 36,
                  fontWeight: 800,
                  lineHeight: 1.15,
                  opacity: nyala ? 1 : 0.55,
                }}
              >
                <div style={{fontSize: 28, color: C.ink2, marginBottom: 6}}>{i + 1}</div>
                {t}
              </div>
              {i < LANGKAH.length - 1 ? <div style={{fontSize: 48, fontWeight: 800, color: C.ink2}}>›</div> : null}
            </React.Fragment>
          );
        })}
      </div>
      <div style={{position: 'absolute', bottom: 120, left: 110, right: 110, textAlign: 'center', fontSize: 40, fontWeight: 700, color: C.ink2}}>
        Setiap langkah divalidasi, dan aksi pentingnya tercatat di log
      </div>
    </Scene>
  );
};

/* ---------- 5. Klasemen ---------- */

type BarisK = {nama: string; warna: string; m: number; mn: number; s: number; k: number; sg: string; poin: number; lolos: boolean};
/* Contoh data: sama dengan skenario head-to-head pada tests/uji-logika.php. */
const KLASEMEN: BarisK[] = [
  {nama: 'Citra', warna: '#1E9E73', m: 3, mn: 2, s: 1, k: 0, sg: '+2', poin: 7, lolos: true},
  {nama: 'Aster', warna: '#2F6BFF', m: 3, mn: 1, s: 1, k: 1, sg: '0', poin: 4, lolos: true},
  {nama: 'Dewa', warna: '#E8553F', m: 3, mn: 1, s: 0, k: 2, sg: '-1', poin: 3, lolos: false},
  {nama: 'Bima', warna: '#7A5AF8', m: 3, mn: 1, s: 0, k: 2, sg: '-1', poin: 3, lolos: false},
];
const KOLOM = '2.4fr 1fr 1fr 1fr 1fr 1fr 1.2fr';

const Sel: React.FC<{teks: string | number; tebal?: boolean}> = ({teks, tebal}) => (
  <div style={{textAlign: 'center', fontWeight: tebal ? 800 : 600, fontSize: tebal ? 46 : 38}}>{teks}</div>
);

const RowK: React.FC<{b: BarisK; i: number}> = ({b, i}) => {
  const frame = useCurrentFrame();
  const p = usePop(24 + i * 14);
  const poin = Math.round(interpolate(frame, [34 + i * 14, 66 + i * 14], [0, b.poin], klem));
  return (
    <div
      style={{
        display: 'grid',
        gridTemplateColumns: KOLOM,
        alignItems: 'center',
        padding: '16px 28px',
        background: b.lolos ? C.mint : C.putih,
        borderTop: `4px solid ${C.ink}`,
        boxShadow: b.lolos ? `inset 14px 0 0 ${C.mintTua}` : 'none',
        opacity: p,
        transform: `translateX(${(1 - p) * 60}px)`,
      }}
    >
      <div style={{display: 'flex', alignItems: 'center', gap: 18, fontSize: 44, fontWeight: 800}}>
        <span style={{width: 30, height: 30, borderRadius: '50%', background: b.warna, border: `4px solid ${C.ink}`}} />
        {b.nama}
      </div>
      <Sel teks={b.m} />
      <Sel teks={b.mn} />
      <Sel teks={b.s} />
      <Sel teks={b.k} />
      <Sel teks={b.sg} />
      <Sel teks={poin} tebal />
    </div>
  );
};

const SceneKlasemen: React.FC<{durasi: number}> = ({durasi}) => {
  const callout = usePop(110);
  return (
    <Scene durasi={durasi}>
      <Kepala kicker="Klasemen" judul="Peringkat dihitung otomatis" />
      <div style={{...kotak(), position: 'absolute', top: 290, left: 110, right: 110, overflow: 'hidden'}}>
        <div style={{display: 'grid', gridTemplateColumns: KOLOM, padding: '16px 28px', background: C.biruMuda, fontSize: 28, fontWeight: 800, color: C.ink2, textAlign: 'center'}}>
          <div style={{textAlign: 'left'}}>TIM</div>
          <div>M</div>
          <div>MN</div>
          <div>S</div>
          <div>K</div>
          <div>SG</div>
          <div>POIN</div>
        </div>
        {KLASEMEN.map((b, i) => (
          <RowK key={b.nama} b={b} i={i} />
        ))}
      </div>
      <div
        style={{
          ...kotak(C.sun),
          position: 'absolute',
          left: 110,
          right: 110,
          bottom: 100,
          padding: '22px 36px',
          fontSize: 38,
          fontWeight: 700,
          lineHeight: 1.3,
          opacity: callout,
          transform: `translateY(${(1 - callout) * 40}px)`,
        }}
      >
        Dewa dan Bima sama-sama 3 poin dengan selisih −1. Dewa menang 1–0 saat bertemu langsung, jadi Dewa di atas (head-to-head).
      </div>
      <Catatan teks="Contoh data · garis hijau = lolos ke babak gugur" />
    </Scene>
  );
};

/* ---------- 6. Bagan ---------- */

const R1: ReadonlyArray<readonly [number, number]> = [[1, 8], [4, 5], [2, 7], [3, 6]];
const SF: ReadonlyArray<readonly [number, number]> = [[1, 4], [2, 3]];
const FINAL: ReadonlyArray<readonly [number, number]> = [[1, 2]];

const BarisTim: React.FC<{seed: number; menang: boolean; garis?: boolean}> = ({seed, menang, garis}) => (
  <div
    style={{
      padding: '14px 22px',
      fontSize: 36,
      fontWeight: menang ? 800 : 600,
      background: menang ? C.mint : C.putih,
      borderTop: garis ? `4px solid ${C.ink}` : 'none',
    }}
  >
    Seed {seed}
  </div>
);

const KolomBagan: React.FC<{
  judul: string;
  pasangan: ReadonlyArray<readonly [number, number]>;
  menang: number[];
  mulai: number;
}> = ({judul, pasangan, menang, mulai}) => {
  const frame = useCurrentFrame();
  const p = interpolate(frame, [mulai, mulai + 14], [0, 1], klem);
  const tandai = frame > mulai + 34;
  return (
    <div style={{display: 'flex', flexDirection: 'column', width: 360, opacity: p, transform: `translateY(${(1 - p) * 30}px)`}}>
      <div style={{fontSize: 28, fontWeight: 800, color: C.ink2, letterSpacing: 2, textTransform: 'uppercase', marginBottom: 14}}>{judul}</div>
      <div style={{display: 'flex', flexDirection: 'column', justifyContent: 'space-around', flex: 1}}>
        {pasangan.map(([a, b], i) => (
          <div key={`${a}-${b}`} style={{...kotak(), borderRadius: 18, boxShadow: `6px 6px 0 ${C.ink}`, overflow: 'hidden'}}>
            <BarisTim seed={a} menang={tandai && menang[i] === a} />
            <BarisTim seed={b} menang={tandai && menang[i] === b} garis />
          </div>
        ))}
      </div>
    </div>
  );
};

const SceneBagan: React.FC<{durasi: number}> = ({durasi}) => {
  const frame = useCurrentFrame();
  const juara = interpolate(frame, [128, 142], [0, 1], klem);
  return (
    <Scene durasi={durasi}>
      <Kepala kicker="Bagan gugur" judul="Seed standar, pemenang maju sendiri" />
      <div style={{position: 'absolute', top: 290, left: 110, right: 110, bottom: 120, display: 'flex', gap: 60}}>
        <KolomBagan judul="Ronde 1" pasangan={R1} menang={[1, 4, 2, 3]} mulai={10} />
        <KolomBagan judul="Semifinal" pasangan={SF} menang={[1, 2]} mulai={48} />
        <KolomBagan judul="Final" pasangan={FINAL} menang={[1]} mulai={86} />
        <div style={{display: 'flex', flexDirection: 'column', width: 360, opacity: juara, transform: `translateY(${(1 - juara) * 30}px)`}}>
          <div style={{fontSize: 28, fontWeight: 800, color: C.ink2, letterSpacing: 2, textTransform: 'uppercase', marginBottom: 14}}>Juara</div>
          <div style={{display: 'flex', flex: 1, alignItems: 'center'}}>
            <div style={{...kotak(C.sun), padding: '30px 34px', fontSize: 52, fontWeight: 800, width: '100%', textAlign: 'center'}}>Seed 1</div>
          </div>
        </div>
      </div>
      <Catatan teks="Ilustrasi 8 tim · jumlah tim ganjil memakai bye" />
    </Scene>
  );
};

/* ---------- 7. Skor aman ---------- */

const LANGKAH_SKOR = [
  'Validasi skor 0–99',
  'Kunci baris dan cek nomor versi',
  'Tentukan pemenang sesuai aturan seri',
  'Simpan skor, kunci laga',
  'Majukan pemenang ke laga berikutnya',
  'Perbarui status turnamen dan catat log',
];

const BarisSkor: React.FC<{teks: string; i: number}> = ({teks, i}) => {
  const p = usePop(14 + i * 14);
  return (
    <div style={{display: 'flex', alignItems: 'center', gap: 24, fontSize: 40, fontWeight: 700, opacity: p, transform: `translateX(${(1 - p) * -60}px)`}}>
      <Nomor n={i + 1} />
      {teks}
    </div>
  );
};

const SceneSkor: React.FC<{durasi: number}> = ({durasi}) => {
  const commit = usePop(104);
  const rollback = usePop(124);
  return (
    <Scene durasi={durasi}>
      <Kepala kicker="Input skor" judul="Satu simpan, satu transaksi" />
      <div style={{...kotak(), position: 'absolute', top: 290, left: 110, width: 1000, padding: '40px 44px', display: 'flex', flexDirection: 'column', gap: 24}}>
        {LANGKAH_SKOR.map((t, i) => (
          <BarisSkor key={t} teks={t} i={i} />
        ))}
      </div>
      <div style={{position: 'absolute', top: 290, left: 1170, right: 110, display: 'flex', flexDirection: 'column', gap: 30}}>
        <div style={{...kotak(C.mint), padding: '30px 34px', opacity: commit, transform: `scale(${0.9 + commit * 0.1})`}}>
          <div style={{fontSize: 56, fontWeight: 800}}>COMMIT</div>
          <div style={{fontSize: 32, fontWeight: 600, marginTop: 8}}>Semua langkah berhasil, laga terkunci</div>
        </div>
        <div style={{...kotak(C.koral), padding: '30px 34px', opacity: rollback, transform: `scale(${0.9 + rollback * 0.1})`}}>
          <div style={{fontSize: 56, fontWeight: 800, color: C.koralTua}}>ROLLBACK</div>
          <div style={{fontSize: 32, fontWeight: 600, marginTop: 8}}>Gagal di langkah mana pun: tidak ada yang berubah</div>
        </div>
        <div style={{fontSize: 32, fontWeight: 700, color: C.ink2, lineHeight: 1.35}}>
          Koreksi hanya oleh admin, wajib beralasan, dan tercatat di log aktivitas.
        </div>
      </div>
    </Scene>
  );
};

/* ---------- 8. Keamanan & offline ---------- */

const AMAN = [
  'Prepared statement',
  'Escape keluaran (anti-XSS)',
  'Token CSRF',
  'Hash kata sandi',
  'Batas 5 kali gagal login',
  'Cek peran di server',
];

const ChipAman: React.FC<{teks: string; i: number}> = ({teks, i}) => {
  const p = usePop(10 + i * 8);
  return (
    <div style={{...kotak(i % 2 === 0 ? C.biruMuda : C.sun), padding: '26px 34px', fontSize: 42, fontWeight: 800, opacity: p, transform: `scale(${0.85 + p * 0.15})`}}>
      {teks}
    </div>
  );
};

const SceneAman: React.FC<{durasi: number}> = ({durasi}) => {
  const offline = usePop(66);
  return (
    <Scene durasi={durasi}>
      <Kepala kicker="Keamanan" judul="Aman dan berjalan tanpa internet" />
      <div style={{position: 'absolute', top: 290, left: 110, right: 110, display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 30}}>
        {AMAN.map((t, i) => (
          <ChipAman key={t} teks={t} i={i} />
        ))}
      </div>
      <div
        style={{
          ...kotak(C.mint),
          position: 'absolute',
          left: 110,
          right: 110,
          bottom: 90,
          padding: '26px 40px',
          fontSize: 40,
          fontWeight: 700,
          textAlign: 'center',
          opacity: offline,
          transform: `translateY(${(1 - offline) * 40}px)`,
        }}
      >
        Offline penuh: tanpa CDN, font web, atau API luar
      </div>
    </Scene>
  );
};

/* ---------- 9. Penutup ---------- */

const ScenePenutup: React.FC<{durasi: number}> = ({durasi}) => {
  const a = usePop(0);
  const b = usePop(14);
  const c = usePop(30);
  return (
    <Scene durasi={durasi}>
      <AbsoluteFill style={{justifyContent: 'center', alignItems: 'center', gap: 28}}>
        <div style={{fontSize: 150, fontWeight: 800, letterSpacing: -4, opacity: a, transform: `translateY(${(1 - a) * 40}px)`}}>Gelanggang</div>
        <div style={{fontSize: 48, fontWeight: 700, color: C.ink2, opacity: b}}>Klasemen, bagan, dan jadwal turnamen sekolah, otomatis</div>
        <div style={{display: 'flex', gap: 18, opacity: c}}>
          <Chip teks="PHP" bg={C.biruMuda} />
          <Chip teks="MySQL" bg={C.mint} />
          <Chip teks="Bootstrap" bg={C.sun} />
        </div>
        <div style={{...kotak(C.putih), padding: '18px 40px', fontSize: 36, fontWeight: 700, opacity: c, marginTop: 10}}>
          github.com/iky-art/gelanggang-project
        </div>
      </AbsoluteFill>
    </Scene>
  );
};

/* ---------- Komposisi utama ---------- */

export const Demo: React.FC = () => (
  <Series>
    <Series.Sequence durationInFrames={DURASI.judul}>
      <SceneJudul durasi={DURASI.judul} />
    </Series.Sequence>
    <Series.Sequence durationInFrames={DURASI.masalah}>
      <SceneMasalah durasi={DURASI.masalah} />
    </Series.Sequence>
    <Series.Sequence durationInFrames={DURASI.fitur}>
      <SceneFitur durasi={DURASI.fitur} />
    </Series.Sequence>
    <Series.Sequence durationInFrames={DURASI.alur}>
      <SceneAlur durasi={DURASI.alur} />
    </Series.Sequence>
    <Series.Sequence durationInFrames={DURASI.klasemen}>
      <SceneKlasemen durasi={DURASI.klasemen} />
    </Series.Sequence>
    <Series.Sequence durationInFrames={DURASI.bagan}>
      <SceneBagan durasi={DURASI.bagan} />
    </Series.Sequence>
    <Series.Sequence durationInFrames={DURASI.skor}>
      <SceneSkor durasi={DURASI.skor} />
    </Series.Sequence>
    <Series.Sequence durationInFrames={DURASI.aman}>
      <SceneAman durasi={DURASI.aman} />
    </Series.Sequence>
    <Series.Sequence durationInFrames={DURASI.penutup}>
      <ScenePenutup durasi={DURASI.penutup} />
    </Series.Sequence>
  </Series>
);
