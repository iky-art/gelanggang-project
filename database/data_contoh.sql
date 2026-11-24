-- ============================================================
--  Data contoh (opsional): 1 turnamen berstatus "Pendaftaran" dengan 8 tim dan pemain.
--  Impor SETELAH gelanggang.sql. Turnamen belum dimulai, jadi kamu bisa mencoba
--  tombol "Mulai turnamen" sendiri dan melihat jadwal serta bagan terbentuk.
--  Semua nama tim/pemain di bawah adalah contoh fiktif untuk latihan.
-- ============================================================
USE `gelanggang`;

INSERT INTO `turnamen`
  (`nama`,`game`,`format`,`jumlah_grup`,`lolos_per_grup`,`pulang_pergi`,`izinkan_seri`,`poin_menang`,`poin_seri`,`poin_kalah`,`juara_tiga`,`durasi_menit`,`status`)
VALUES
  ('Contoh Cup: Antar Kelas','Mobile Legends','liga_gugur',2,2,0,0,3,1,0,1,45,'draft');
SET @t = LAST_INSERT_ID();

INSERT INTO `tim` (`turnamen_id`,`nama`,`singkatan`,`warna`,`seed`) VALUES
  (@t,'Garuda XII RPL','GRD','#2F6BFF',1),
  (@t,'Macan TKJ','MCN','#E8553F',2),
  (@t,'Elang Multimedia','ELG','#1E9E73',3),
  (@t,'Naga Akuntansi','NGA','#B8860B',4),
  (@t,'Rajawali X RPL','RJW','#7A5AF8',5),
  (@t,'Singa TKJ B','SNG','#D9503F',6),
  (@t,'Cakra Broadcasting','CKR','#0E8FB3',7),
  (@t,'Baruna Perhotelan','BRN','#C2410C',8);

INSERT INTO `pemain` (`tim_id`,`nama`,`nickname`,`peran`,`kapten`)
SELECT t.`id`, CONCAT('Pemain ', n.`no`, ' ', t.`singkatan`), CONCAT(t.`singkatan`, n.`no`),
       ELT(n.`no`,'Tank','Jungler','Mage','Marksman','Support'), IF(n.`no`=1,1,0)
FROM `tim` t
JOIN (SELECT 1 AS `no` UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5) n
WHERE t.`turnamen_id` = @t;
