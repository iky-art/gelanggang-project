-- ============================================================
--  Gelanggang v1.0.0 - skema database
--  Impor lewat phpMyAdmin: tab "Impor" -> pilih file ini -> Kirim.
--  Kompatibel MySQL 5.7+ / MySQL 8 / MariaDB 10.4+.
-- ============================================================

CREATE DATABASE IF NOT EXISTS `gelanggang`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `gelanggang`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `percobaan_login`, `log_aktivitas`, `pertandingan`, `pemain`, `tim`, `grup`, `turnamen`, `pengguna`;
SET FOREIGN_KEY_CHECKS = 1;

-- Akun pengelola. Akun pertama dibuat lewat halaman setup.php (tidak ada sandi bawaan).
CREATE TABLE `pengguna` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama`        VARCHAR(80)  NOT NULL,
  `username`    VARCHAR(40)  NOT NULL,
  `kata_sandi`  VARCHAR(255) NOT NULL,
  `peran`       ENUM('admin','panitia') NOT NULL DEFAULT 'panitia',
  `aktif`       TINYINT(1)   NOT NULL DEFAULT 1,
  `dibuat_pada` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pengguna_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `turnamen` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama`           VARCHAR(120) NOT NULL,
  `game`           VARCHAR(60)  NOT NULL,
  `format`         ENUM('liga_gugur','liga','gugur') NOT NULL DEFAULT 'liga_gugur',
  `jumlah_grup`    TINYINT UNSIGNED NOT NULL DEFAULT 2,
  `lolos_per_grup` TINYINT UNSIGNED NOT NULL DEFAULT 2,
  `pulang_pergi`   TINYINT(1) NOT NULL DEFAULT 0,
  `izinkan_seri`   TINYINT(1) NOT NULL DEFAULT 0,
  `poin_menang`    TINYINT UNSIGNED NOT NULL DEFAULT 3,
  `poin_seri`      TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `poin_kalah`     TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `juara_tiga`     TINYINT(1) NOT NULL DEFAULT 0,
  `durasi_menit`   SMALLINT UNSIGNED NOT NULL DEFAULT 60,
  `status`         ENUM('draft','grup','gugur','selesai') NOT NULL DEFAULT 'draft',
  `juara_id`       INT UNSIGNED NULL,
  `dibuat_pada`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `grup` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `turnamen_id` INT UNSIGNED NOT NULL,
  `nama`        VARCHAR(20)  NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_grup_nama` (`turnamen_id`,`nama`),
  CONSTRAINT `fk_grup_turnamen` FOREIGN KEY (`turnamen_id`) REFERENCES `turnamen`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tim` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `turnamen_id` INT UNSIGNED NOT NULL,
  `grup_id`     INT UNSIGNED NULL,
  `nama`        VARCHAR(60)  NOT NULL,
  `singkatan`   VARCHAR(5)   NOT NULL,
  `warna`       CHAR(7)      NOT NULL DEFAULT '#2F6BFF',
  `seed`        SMALLINT UNSIGNED NULL,
  `undian`      SMALLINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tim_nama` (`turnamen_id`,`nama`),
  CONSTRAINT `fk_tim_turnamen` FOREIGN KEY (`turnamen_id`) REFERENCES `turnamen`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tim_grup` FOREIGN KEY (`grup_id`) REFERENCES `grup`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pemain` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tim_id`      INT UNSIGNED NOT NULL,
  `nama`        VARCHAR(80)  NOT NULL,
  `nickname`    VARCHAR(40)  NOT NULL,
  `peran`       VARCHAR(30)  NULL,
  `kapten`      TINYINT(1)   NOT NULL DEFAULT 0,
  `dibuat_pada` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pemain_nick` (`tim_id`,`nickname`),
  CONSTRAINT `fk_pemain_tim` FOREIGN KEY (`tim_id`) REFERENCES `tim`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- menang_ke_id / kalah_ke_id menunjuk laga berikutnya di bagan gugur.
-- Sengaja tanpa foreign key (self-reference) agar penghapusan turnamen tetap bersih.
CREATE TABLE `pertandingan` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `turnamen_id`     INT UNSIGNED NOT NULL,
  `fase`            ENUM('grup','gugur') NOT NULL,
  `grup_id`         INT UNSIGNED NULL,
  `ronde`           TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `urutan`          SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  `label`           VARCHAR(40)  NULL,
  `tim_a_id`        INT UNSIGNED NULL,
  `tim_b_id`        INT UNSIGNED NULL,
  `skor_a`          SMALLINT UNSIGNED NULL,
  `skor_b`          SMALLINT UNSIGNED NULL,
  `pemenang_id`     INT UNSIGNED NULL,
  `bye`             TINYINT(1) NOT NULL DEFAULT 0,
  `status`          ENUM('terjadwal','selesai') NOT NULL DEFAULT 'terjadwal',
  `dikunci`         TINYINT(1) NOT NULL DEFAULT 0,
  `waktu`           DATETIME NULL,
  `lokasi`          VARCHAR(60) NULL,
  `menang_ke_id`    INT UNSIGNED NULL,
  `menang_ke_slot`  ENUM('a','b') NULL,
  `kalah_ke_id`     INT UNSIGNED NULL,
  `kalah_ke_slot`   ENUM('a','b') NULL,
  `versi`           INT UNSIGNED NOT NULL DEFAULT 0,
  `diperbarui_pada` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_laga_turnamen` (`turnamen_id`,`fase`,`ronde`,`urutan`),
  KEY `idx_laga_waktu` (`waktu`),
  CONSTRAINT `fk_laga_turnamen` FOREIGN KEY (`turnamen_id`) REFERENCES `turnamen`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_laga_grup` FOREIGN KEY (`grup_id`) REFERENCES `grup`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_laga_tim_a` FOREIGN KEY (`tim_a_id`) REFERENCES `tim`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_laga_tim_b` FOREIGN KEY (`tim_b_id`) REFERENCES `tim`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_laga_pemenang` FOREIGN KEY (`pemenang_id`) REFERENCES `tim`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `log_aktivitas` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pengguna_id`   INT UNSIGNED NULL,
  `nama_pengguna` VARCHAR(80)  NOT NULL,
  `aksi`          VARCHAR(40)  NOT NULL,
  `detail`        TEXT NULL,
  `ip`            VARCHAR(45)  NULL,
  `waktu`         DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_log_waktu` (`waktu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `percobaan_login` (
  `id`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(40) NOT NULL,
  `ip`       VARCHAR(45) NOT NULL,
  `waktu`    DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_percobaan` (`username`,`ip`,`waktu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
