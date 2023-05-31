<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu_model extends CI_Model
{

    // public function getPasienMeninggalConfirmNonKomorbid($tanggal)
    // {
    //     return $this->db_kedua->query("Select SUM(Jumlah) AS Jumlah From V_4PasienMeninggalConfirmNonKomorbid where Convert(date, TglPendaftaran) = '$tanggal'")->result();
    // }

    // public function getPasienMeninggalConfirmKomorbid($tanggal)
    // {
    //     return $this->db_kedua->query("Select SUM(Jumlah) AS Jumlah From V_4PasienMeninggalConfirmKomorbid where Convert(date, TglPendaftaran) = '$tanggal'")->result();
    // }

    // public function getPasienPulangSembuh($tanggal)
    // {
    //     return $this->db_kedua->query("Select SUM(Jumlah) AS Jumlah From V_4PasienPulangSembuhCovid where Convert(date, TglPendaftaran) = '$tanggal'")->result();
    // }

    public function getSubMenu()
    {
        $query = "SELECT     user_sub_menu.id, user_sub_menu.menu_id, user_sub_menu.title, user_sub_menu.url, user_sub_menu.icon, user_sub_menu.is_active, user_menuNew.menu
        , Case When user_sub_menu.is_active = '1' Then 'Active' Else 'Non Active' End AS StatusActive, user_sub_menu.NoUrut FROM  user_sub_menu INNER JOIN user_menuNew ON user_sub_menu.menu_id = user_menuNew.id";
        return $this->db->query($query)->result_array();
    }

    public function GetPasienPulangSembuh($nolab)
    {
        return $this->db_kedua->query("Select Tanggal, SUM(Jumlah) AS Jumlah from (
            SELECT     COUNT(DISTINCT RegistrasiLaboratorium.NoPendaftaran) AS Jumlah, CONVERT(date, RegistrasiLaboratorium.TglPendaftaran) AS Tanggal
            FROM         RegistrasiLaboratorium INNER JOIN PasienKeluarKamar ON RegistrasiLaboratorium.NoPendaftaran = PasienKeluarKamar.NoPendaftaran
            WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ($nolab)) and (PasienKeluarKamar.KdStatusKeluar = '02') and (PasienKeluarKamar.KdKondisiKeluar = '01') Group By CONVERT(date, RegistrasiLaboratorium.TglPendaftaran)
            UNION ALL
            SELECT     COUNT(DISTINCT RegistrasiLaboratorium.NoPendaftaran) AS Jumlah, CONVERT(date, RegistrasiLaboratorium.TglPendaftaran) AS Tanggal
            FROM         RegistrasiLaboratorium INNER JOIN PasienIGDKeluar ON RegistrasiLaboratorium.NoPendaftaran = PasienIGDKeluar.NoPendaftaran
            WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ($nolab)) and (PasienIGDKeluar.KdStatusKeluar = '02') and (PasienIGDKeluar.KdKondisiPulang = '01') Group By CONVERT(date, RegistrasiLaboratorium.TglPendaftaran)
            ) AS Data Group By Tanggal")->result();
    }

    public function GetPasienPulangMeninggalKomorbid($nolab)
    {
        return $this->db_kedua->query("Select Tanggal, SUM(Jumlah) AS Jumlah from (
            SELECT     COUNT(DISTINCT RegistrasiLaboratorium.NoPendaftaran) AS Jumlah, CONVERT(date, RegistrasiLaboratorium.TglPendaftaran) AS Tanggal
            FROM         RegistrasiLaboratorium INNER JOIN PasienKeluarKamar ON RegistrasiLaboratorium.NoPendaftaran = PasienKeluarKamar.NoPendaftaran
            WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ($nolab)) and (PasienKeluarKamar.KdStatusKeluar IN ('03','04','11','12')) and ((select COUNT(NoPendaftaran) from PeriksaDiagnosa where KdJenisDiagnosa = '11' and NoPendaftaran = RegistrasiLaboratorium.NoPendaftaran) >= 1) Group By CONVERT(date, RegistrasiLaboratorium.TglPendaftaran), RegistrasiLaboratorium.NoPendaftaran
            UNION ALL
            SELECT     COUNT(DISTINCT RegistrasiLaboratorium.NoPendaftaran) AS Jumlah, CONVERT(date, RegistrasiLaboratorium.TglPendaftaran) AS Tanggal
            FROM         RegistrasiLaboratorium INNER JOIN PasienIGDKeluar ON RegistrasiLaboratorium.NoPendaftaran = PasienIGDKeluar.NoPendaftaran
            WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ($nolab)) and (PasienIGDKeluar.KdStatusKeluar IN ('03','04','11','12'))  and ((select COUNT(NoPendaftaran) from PeriksaDiagnosa where KdJenisDiagnosa = '11' and NoPendaftaran = RegistrasiLaboratorium.NoPendaftaran) >= 1) Group By CONVERT(date, RegistrasiLaboratorium.TglPendaftaran), RegistrasiLaboratorium.NoPendaftaran
            ) AS Data Group By Tanggal")->result();
    }

    public function GetPasienPulangMeninggalTanpaKomorbid($nolab)
    {
        return $this->db_kedua->query("Select Tanggal, SUM(Jumlah) AS Jumlah from (
            SELECT     COUNT(DISTINCT RegistrasiLaboratorium.NoPendaftaran) AS Jumlah, CONVERT(date, RegistrasiLaboratorium.TglPendaftaran) AS Tanggal
            FROM         RegistrasiLaboratorium INNER JOIN PasienKeluarKamar ON RegistrasiLaboratorium.NoPendaftaran = PasienKeluarKamar.NoPendaftaran
            WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ($nolab)) and (PasienKeluarKamar.KdStatusKeluar IN ('03','04','11','12')) and ((select COUNT(NoPendaftaran) from PeriksaDiagnosa where KdJenisDiagnosa = '11' and NoPendaftaran = RegistrasiLaboratorium.NoPendaftaran) < 1) Group By CONVERT(date, RegistrasiLaboratorium.TglPendaftaran), RegistrasiLaboratorium.NoPendaftaran
            UNION ALL
            SELECT     COUNT(DISTINCT RegistrasiLaboratorium.NoPendaftaran) AS Jumlah, CONVERT(date, RegistrasiLaboratorium.TglPendaftaran) AS Tanggal
            FROM         RegistrasiLaboratorium INNER JOIN PasienIGDKeluar ON RegistrasiLaboratorium.NoPendaftaran = PasienIGDKeluar.NoPendaftaran
            WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ($nolab)) and (PasienIGDKeluar.KdStatusKeluar IN ('03','04','11','12')) and ((select COUNT(NoPendaftaran) from PeriksaDiagnosa where KdJenisDiagnosa = '11' and NoPendaftaran = RegistrasiLaboratorium.NoPendaftaran) < 1)  Group By CONVERT(date, RegistrasiLaboratorium.TglPendaftaran), RegistrasiLaboratorium.NoPendaftaran
            ) AS Data Group By Tanggal")->result();
    }

    public function GetPasienPulangDirujuk($nolab)
    {
        return $this->db_kedua->query("Select Tanggal, SUM(Jumlah) AS Jumlah from (
            SELECT     COUNT(DISTINCT RegistrasiLaboratorium.NoPendaftaran) AS Jumlah, CONVERT(date, RegistrasiLaboratorium.TglPendaftaran) AS Tanggal
            FROM         RegistrasiLaboratorium INNER JOIN PasienKeluarKamar ON RegistrasiLaboratorium.NoPendaftaran = PasienKeluarKamar.NoPendaftaran
            WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ($nolab)) and (PasienKeluarKamar.KdStatusKeluar = '06') Group By CONVERT(date, RegistrasiLaboratorium.TglPendaftaran)
            UNION ALL
            SELECT     COUNT(DISTINCT RegistrasiLaboratorium.NoPendaftaran) AS Jumlah, CONVERT(date, RegistrasiLaboratorium.TglPendaftaran) AS Tanggal
            FROM         RegistrasiLaboratorium INNER JOIN PasienIGDKeluar ON RegistrasiLaboratorium.NoPendaftaran = PasienIGDKeluar.NoPendaftaran
            WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ($nolab)) and (PasienIGDKeluar.KdStatusKeluar = '06')  Group By CONVERT(date, RegistrasiLaboratorium.TglPendaftaran)
            ) AS Data Group By Tanggal")->result();
    }

    public function GetPasienPulangISMAN($nolab)
    {
        return $this->db_kedua->query("Select Tanggal, SUM(Jumlah) AS Jumlah from (
            SELECT     COUNT(DISTINCT RegistrasiLaboratorium.NoPendaftaran) AS Jumlah, CONVERT(date, RegistrasiLaboratorium.TglPendaftaran) AS Tanggal
            FROM         RegistrasiLaboratorium INNER JOIN Ruangan ON RegistrasiLaboratorium.KdRuanganPerujuk = Ruangan.KdRuangan
            WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ($nolab)) AND (Ruangan.KdInstalasi = '02')
            GROUP BY CONVERT(date, RegistrasiLaboratorium.TglPendaftaran)
                        UNION ALL
            SELECT     COUNT(DISTINCT RegistrasiLaboratorium.NoPendaftaran) AS Jumlah, CONVERT(date, RegistrasiLaboratorium.TglPendaftaran) AS Tanggal
            FROM         RegistrasiLaboratorium LEFT OUTER JOIN PasienIGDKeluar ON RegistrasiLaboratorium.NoPendaftaran = PasienIGDKeluar.NoPendaftaran
            WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ($nolab)) AND (RegistrasiLaboratorium.KdRuanganPerujuk = '001') AND (PasienIGDKeluar.TglKeluar IS NULL) GROUP BY CONVERT(date, RegistrasiLaboratorium.TglPendaftaran),  RegistrasiLaboratorium.KdRuanganPerujuk
                        ) AS Data Group By Tanggal")->result();
    }

    public function GetPasienPulangAPS($nolab)
    {
        return $this->db_kedua->query("Select Tanggal, SUM(Jumlah) AS Jumlah from (
            SELECT     COUNT(DISTINCT RegistrasiLaboratorium.NoPendaftaran) AS Jumlah, CONVERT(date, RegistrasiLaboratorium.TglPendaftaran) AS Tanggal
            FROM         RegistrasiLaboratorium INNER JOIN PasienKeluarKamar ON RegistrasiLaboratorium.NoPendaftaran = PasienKeluarKamar.NoPendaftaran
            WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ($nolab)) and (PasienKeluarKamar.KdStatusKeluar IN ('05','09','10')) Group By CONVERT(date, RegistrasiLaboratorium.TglPendaftaran)
            UNION ALL
            SELECT     COUNT(DISTINCT RegistrasiLaboratorium.NoPendaftaran) AS Jumlah, CONVERT(date, RegistrasiLaboratorium.TglPendaftaran) AS Tanggal
            FROM         RegistrasiLaboratorium INNER JOIN PasienIGDKeluar ON RegistrasiLaboratorium.NoPendaftaran = PasienIGDKeluar.NoPendaftaran
            WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ($nolab)) and (PasienIGDKeluar.KdStatusKeluar IN ('05','09','10'))  Group By CONVERT(date, RegistrasiLaboratorium.TglPendaftaran)
            ) AS Data Group By Tanggal")->result();
    }

    public function GetPasienMeninggalProbKomorbid($nolab)
    {
        return $this->db_kedua->query("Select Tanggal, SUM([0-6Hari]) AS satu, SUM([6-28Hari]) AS dua, SUM([29Hari-<1Thn]) AS tiga, SUM([1-4Thn]) AS empat, SUM([5-18Thn]) AS lima, SUM([19-40Thn]) AS enam, SUM([41-60Thn]) AS tujuh, SUM([>60Thn]) AS delapan from (
            Select Tanggal, NoPendaftaran, Usia, Case When UsiaTahun < 1 and UsiaHari <= 6 Then 1 else 0 End AS '0-6Hari', case when UsiaTahun < 1 and UsiaHari >  6 and UsiaHari <= 28 Then 1 Else 0 End AS '6-28Hari', case when UsiaTahun < 1 and UsiaHari > 28 Then 1 Else 0 End AS '29Hari-<1Thn', Case when UsiaTahun >= 1 and UsiaTahun < 5 Then 1 Else 0 End AS '1-4Thn', case when UsiaTahun >= 5 and UsiaTahun < 19 Then 1 Else 0 End AS '5-18Thn',
             Case when UsiaTahun >= 19 and UsiaTahun < 41 Then 1 Else 0 End AS '19-40Thn', case When UsiaTahun >= 41 and UsiaTahun < 61 Then 1 Else 0 End AS '41-60Thn', Case When UsiaTahun >= 61 Then 1 Else 0 End As '>60Thn' from (
            SELECT DISTINCT CONVERT(date, RegistrasiLaboratorium.TglPendaftaran) AS Tanggal, RegistrasiLaboratorium.NoPendaftaran, DATEDIFF(DAY, pasien.TglLahir, RegistrasiLaboratorium.TglPendaftaran) AS UsiaHari, dbo.S_HitungUmurTahun(pasien.TglLahir, RegistrasiLaboratorium.TglPendaftaran) AS UsiaTahun, dbo.S_HitungUmur(pasien.TglLahir, RegistrasiLaboratorium.TglPendaftaran) AS Usia
            FROM RegistrasiLaboratorium INNER JOIN PasienKeluarKamar ON RegistrasiLaboratorium.NoPendaftaran = PasienKeluarKamar.NoPendaftaran INNER JOIN
            Pasien ON PasienKeluarKamar.NoCM = Pasien.NoCM WHERE (RegistrasiLaboratorium.NoLaboratorium IN ($nolab)) AND (PasienKeluarKamar.KdStatusKeluar IN 
            ('03', '04', '11', '12')) AND ((SELECT COUNT(NoPendaftaran) AS Expr1 FROM PeriksaDiagnosa WHERE (KdJenisDiagnosa = '11') AND 
            (NoPendaftaran = RegistrasiLaboratorium.NoPendaftaran)) >= 1) ) AS Data ) AS Data2 Group By Tanggal")->result();
    }

    public function GetPasienMeninggalProbNonKomorbid($nolab)
    {
        return $this->db_kedua->query("Select Tanggal, SUM([0-6Hari]) AS satu, SUM([6-28Hari]) AS dua, SUM([29Hari-<1Thn]) AS tiga, SUM([1-4Thn]) AS empat, SUM([5-18Thn]) AS lima, SUM([19-40Thn]) AS enam, SUM([41-60Thn]) AS tujuh, SUM([>60Thn]) AS delapan from (
            Select Tanggal, NoPendaftaran, Usia, Case When UsiaTahun < 1 and UsiaHari <= 6 Then 1 else 0 End AS '0-6Hari', case when UsiaTahun < 1 and UsiaHari >  6 and UsiaHari <= 28 Then 1 Else 0 End AS '6-28Hari', case when UsiaTahun < 1 and UsiaHari > 28 Then 1 Else 0 End AS '29Hari-<1Thn', Case when UsiaTahun >= 1 and UsiaTahun < 5 Then 1 Else 0 End AS '1-4Thn', case when UsiaTahun >= 5 and UsiaTahun < 19 Then 1 Else 0 End AS '5-18Thn',
             Case when UsiaTahun >= 19 and UsiaTahun < 41 Then 1 Else 0 End AS '19-40Thn', case When UsiaTahun >= 41 and UsiaTahun < 61 Then 1 Else 0 End AS '41-60Thn', Case When UsiaTahun >= 61 Then 1 Else 0 End As '>60Thn' from (
            SELECT DISTINCT CONVERT(date, RegistrasiLaboratorium.TglPendaftaran) AS Tanggal, RegistrasiLaboratorium.NoPendaftaran, DATEDIFF(DAY, pasien.TglLahir, RegistrasiLaboratorium.TglPendaftaran) AS UsiaHari, dbo.S_HitungUmurTahun(pasien.TglLahir, RegistrasiLaboratorium.TglPendaftaran) AS UsiaTahun, dbo.S_HitungUmur(pasien.TglLahir, RegistrasiLaboratorium.TglPendaftaran) AS Usia
            FROM RegistrasiLaboratorium INNER JOIN PasienKeluarKamar ON RegistrasiLaboratorium.NoPendaftaran = PasienKeluarKamar.NoPendaftaran INNER JOIN
            Pasien ON PasienKeluarKamar.NoCM = Pasien.NoCM WHERE (RegistrasiLaboratorium.NoLaboratorium IN ($nolab)) AND (PasienKeluarKamar.KdStatusKeluar IN 
            ('03', '04', '11', '12')) AND ((SELECT COUNT(NoPendaftaran) AS Expr1 FROM PeriksaDiagnosa WHERE (KdJenisDiagnosa = '11') AND 
            (NoPendaftaran = RegistrasiLaboratorium.NoPendaftaran)) < 1) ) AS Data ) AS Data2 Group By Tanggal")->result();
    }

    public function GetPasienKomorbid($nolab)
    {
        return $this->db_kedua->query("SELECT Tanggal, SUM(ICUTekananNegatifDenganVentilatorL) AS ICUTekananNegatifDenganVentilatorL, SUM(ICUTekananNegatifDenganVentilatorP) AS ICUTekananNegatifDenganVentilatorP, 
        SUM(ICUTekananNegatifTanpaVentilatorL) AS ICUTekananNegatifTanpaVentilatorL, SUM(ICUTekananNegatifTanpaVentilatorP) AS ICUTekananNegatifTanpaVentilatorP, SUM(IsolasiTekananNegatifL) 
        AS IsolasiTekananNegatifL, SUM(IsolasiTekananNegatifP) AS IsolasiTekananNegatifP, SUM(IsolasiTanpaTekananNegatifL) AS IsolasiTanpaTekananNegatifL, SUM(IsolasiTanpaTekananNegatifP) 
        AS IsolasiTanpaTekananNegatifP FROM V_4LapCovidKomorbid WHERE (NoLaboratorium IN ($nolab)) GROUP BY Tanggal")->result();
        // $query = "SELECT Tanggal, SUM(ICUTekananNegatifDenganVentilatorL) AS ICUTekananNegatifDenganVentilatorL, SUM(ICUTekananNegatifDenganVentilatorP) AS ICUTekananNegatifDenganVentilatorP, 
        //  SUM(ICUTekananNegatifTanpaVentilatorL) AS ICUTekananNegatifTanpaVentilatorL, SUM(ICUTekananNegatifTanpaVentilatorP) AS ICUTekananNegatifTanpaVentilatorP, SUM(IsolasiTekananNegatifL) 
        //  AS IsolasiTekananNegatifL, SUM(IsolasiTekananNegatifP) AS IsolasiTekananNegatifP, SUM(IsolasiTanpaTekananNegatifL) AS IsolasiTanpaTekananNegatifL, SUM(IsolasiTanpaTekananNegatifP) 
        //  AS IsolasiTanpaTekananNegatifP FROM V_4LapCovidKomorbid WHERE (NoLaboratorium IN ($nolab)) GROUP BY Tanggal";
        // return $this->db->query($query)->result_array();
        // echo $query;
        // die();
    }

    public function GetPasienNonKomorbid($nolab)
    {
        return $this->db_kedua->query("SELECT Tanggal, SUM(ICUTekananNegatifDenganVentilatorL) AS ICUTekananNegatifDenganVentilatorL, SUM(ICUTekananNegatifDenganVentilatorP) AS ICUTekananNegatifDenganVentilatorP, 
        SUM(ICUTekananNegatifTanpaVentilatorL) AS ICUTekananNegatifTanpaVentilatorL, SUM(ICUTekananNegatifTanpaVentilatorP) AS ICUTekananNegatifTanpaVentilatorP, SUM(IsolasiTekananNegatifL) 
        AS IsolasiTekananNegatifL, SUM(IsolasiTekananNegatifP) AS IsolasiTekananNegatifP, SUM(IsolasiTanpaTekananNegatifL) AS IsolasiTanpaTekananNegatifL, SUM(IsolasiTanpaTekananNegatifP) 
        AS IsolasiTanpaTekananNegatifP FROM V_4LapCovidNonKomorbid WHERE (NoLaboratorium IN ($nolab)) GROUP BY Tanggal")->result();
        // $query = "SELECT Tanggal, SUM(ICUTekananNegatifDenganVentilatorL) AS ICUTekananNegatifDenganVentilatorL, SUM(ICUTekananNegatifDenganVentilatorP) AS ICUTekananNegatifDenganVentilatorP, 
        // SUM(ICUTekananNegatifTanpaVentilatorL) AS ICUTekananNegatifTanpaVentilatorL, SUM(ICUTekananNegatifTanpaVentilatorP) AS ICUTekananNegatifTanpaVentilatorP, SUM(IsolasiTekananNegatifL) 
        // AS IsolasiTekananNegatifL, SUM(IsolasiTekananNegatifP) AS IsolasiTekananNegatifP, SUM(IsolasiTanpaTekananNegatifL) AS IsolasiTanpaTekananNegatifL, SUM(IsolasiTanpaTekananNegatifP) 
        // AS IsolasiTanpaTekananNegatifP FROM V_4LapCovidNonKomorbid WHERE (NoLaboratorium IN ($nolab)) GROUP BY Tanggal";
        // return $this->db->query($query)->result_array();
        // echo $query;
        // die();
    }

    public function GetPasienSuspectDetail($nolab)
    {
        // $this->db_kedua = $this->load->database('db2', true);
        return $this->db_kedua->query("SELECT  distinct   RegistrasiLaboratorium.NoLaboratorium, RegistrasiLaboratorium.TglPendaftaran, RegistrasiLaboratorium.NamaRujukanAsal, RegistrasiLaboratorium.NamaPerujuk, Pasien.NoRM, 
        Pasien.NamaLengkap, Pasien.TglLahir, Pasien.JenisKelamin, Pasien.Alamat
FROM         RegistrasiLaboratorium INNER JOIN
        Pasien ON RegistrasiLaboratorium.NoCM = Pasien.NoCM Where RegistrasiLaboratorium.NoLaboratorium IN ($nolab)")->result();
    }

    public function GetPasienConfirmDetailMasuk($nolab, $Tanggal, $logo)
    {
        // $this->db_kedua = $this->load->database('db2', true);
        return $this->db_kedua->query("SELECT DISTINCT  ROW_NUMBER() OVER (ORDER BY RegistrasiLaboratorium.NoPendaftaran) AS Nomor, RegistrasiLaboratorium.NoPendaftaran, PasienDaftar.TglPendaftaran, Ruangan.NamaRuangan AS RuangRawat, Pasien.NoRM, Pasien.NamaLengkap, Pasien.TglLahir, Pasien.JenisKelamin, 
        Pasien.Alamat, PasienDaftar.TglPulang, 'Positif Pemeriksaan dilakukan di Laboratorium rujukan' AS HasilLab, dbo.Ambil_DiagnosaUtama(PasienDaftar.NoPendaftaran) AS Diagnosa
        FROM  RegistrasiLaboratorium INNER JOIN PasienDaftar ON RegistrasiLaboratorium.NoPendaftaran = PasienDaftar.NoPendaftaran INNER JOIN
        Ruangan ON PasienDaftar.KdRuanganAkhir = Ruangan.KdRuangan INNER JOIN Pasien ON PasienDaftar.NoCM = Pasien.NoCM
        WHERE (RegistrasiLaboratorium.NoLaboratorium IN ($nolab)) AND (Ruangan.KdInstalasi = '03') AND (CONVERT(date, PasienDaftar.TglPendaftaran) = '$Tanggal') AND (PasienDaftar.KdRuanganAkhir $logo '304')")->result();
    }

    public function GetPasienSuspect($nolab)
    {
        // $this->db_kedua = $this->load->database('db2', true);
        return $this->db_kedua->query("SELECT Tanggal, SUM(UGDL) AS UGDL, SUM(UGDP) AS UGDP, SUM(RajalL) AS RajalL, SUM(RajalP) AS RajalP, SUM(RanapL) AS RanapL, SUM(RanapP) AS RanapP
        FROM V_4SuspectCOVID  WHERE (NoLaboratorium IN ($nolab)) Group BY Tanggal")->result();
    }

    public function GetNoLaboratoriumSuspectRanap($tanggal)
    {
        return $this->db_empat->query("SELECT DISTINCT RESULT_DATA.his_reg_no FROM RESULT_DATA INNER JOIN PATIENT_REGISTRATION ON RESULT_DATA.lis_reg_no = 
        PATIENT_REGISTRATION.LIS_REG_NO WHERE (RESULT_DATA.HIS_TEST_PARENT_ID = '921941') AND (RESULT_DATA.his_reg_no <> '') AND 
        (PATIENT_REGISTRATION.WARD_POLI_ID IN ('601','209','222', '312','304')) AND (CONVERT(date, PATIENT_REGISTRATION.REG_DT) = '$tanggal')")->result();
    }

    public function GetNoLaboratoriumConfirmRanap($tanggal)
    {
        return $this->db_empat->query("SELECT DISTINCT RESULT_DATA.his_reg_no FROM RESULT_DATA INNER JOIN PATIENT_REGISTRATION ON RESULT_DATA.lis_reg_no = 
        PATIENT_REGISTRATION.LIS_REG_NO WHERE (RESULT_DATA.HIS_TEST_PARENT_ID = '921941') AND (RESULT_DATA.his_reg_no <> '') AND 
        (PATIENT_REGISTRATION.WARD_POLI_ID IN ('601','209','222', '312','304')) AND (RESULT_DATA.result like '%positif%') AND 
        (CONVERT(date, PATIENT_REGISTRATION.REG_DT) = '$tanggal')")->result();
    }

    public function GetNoLaboratoriumSuspect($tanggal)
    {
        // $this->db_empat = $this->load->database('db4', true);
        return $this->db_empat->query("select distinct RESULT_DATA.his_reg_no from RESULT_DATA INNER JOIN
        PATIENT_REGISTRATION ON RESULT_DATA.lis_reg_no = PATIENT_REGISTRATION.LIS_REG_NO where RESULT_DATA.HIS_TEST_PARENT_ID = '921941' and Convert(date, PATIENT_REGISTRATION.REG_DT) = '$tanggal' and RESULT_DATA.his_reg_no <> ''")->result();
    }

    public function GetNoLaboratoriumSuspectByTanggal($tanggal1, $tanggal2)
    {
        // $this->db_empat = $this->load->database('db4', true);
        $q = $this->db_empat->query("select distinct RESULT_DATA.his_reg_no from RESULT_DATA INNER JOIN
        PATIENT_REGISTRATION ON RESULT_DATA.lis_reg_no = PATIENT_REGISTRATION.LIS_REG_NO where RESULT_DATA.HIS_TEST_PARENT_ID = '921941' and Convert(date, PATIENT_REGISTRATION.REG_DT) between '$tanggal1' and '$tanggal2' and RESULT_DATA.his_reg_no <> ''");
        return $q->result();
    }

    public function GetIdPegawai($tgllahir, $namalengkap)
    {
        var_dump($tgllahir);
        var_dump($namalengkap);
        die;
        // $this->db_empat = $this->load->database('db4', true);
        // $q = $this->db_kedua->query("select IdPegawai from DataPegawaiPHL where IdPegawai = 'L031556571' order by TglMasuk desc");
        // return $q->result();
    }



    public function GetIdPegawai2()
    {
        // $this->db_empat = $this->load->database('db4', true);
        $q = $this->db_kedua->query("select IdPegawai from DataPegawaiPHL");
        return $q->result();
    }

    public function GetNoLaboratoriumConfirmByTanggal($tanggal1, $tanggal2)
    {
        // $this->db_empat = $this->load->database('db4', true);
        $q = $this->db_empat->query("select distinct RESULT_DATA.his_reg_no from RESULT_DATA INNER JOIN
        PATIENT_REGISTRATION ON RESULT_DATA.lis_reg_no = PATIENT_REGISTRATION.LIS_REG_NO where RESULT_DATA.HIS_TEST_PARENT_ID = '921941' and Convert(date, PATIENT_REGISTRATION.REG_DT) between '$tanggal1' and '$tanggal2' and RESULT_DATA.result like '%Positif%' and RESULT_DATA.his_reg_no <> ''");
        return $q->result();
    }

    public function getnopen($noLab, $tanggal2)
    {
        // $this->db_empat = $this->load->database('db4', true);
        $q = $this->db_empat->query("SELECT DISTINCT RegistrasiLaboratorium.NoPendaftaran
        FROM         RegistrasiLaboratorium INNER JOIN PasienMasukRumahSakit ON RegistrasiLaboratorium.NoPendaftaran = PasienMasukRumahSakit.NoPendaftaran
        WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ()) AND (PasienMasukRumahSakit.KdRuangan = '001') AND (PasienMasukRumahSakit.StatusPeriksa = 'Y') AND (Convert(date, PasienMasukRumahSakit.TglMasuk) between '2021-06-01' and '2021-08-27')");
        return $q->result();
    }

    public function GetNoLaboratoriumConfirmByBulanTahun($Bulan, $Tahun)
    {
        // $this->db_empat = $this->load->database('db4', true);
        $q = $this->db_empat->query("select distinct RESULT_DATA.his_reg_no from RESULT_DATA INNER JOIN
        PATIENT_REGISTRATION ON RESULT_DATA.lis_reg_no = PATIENT_REGISTRATION.LIS_REG_NO where RESULT_DATA.HIS_TEST_PARENT_ID = '921941' and MONTH(PATIENT_REGISTRATION.REG_DT) = '$Bulan' and YEAR(PATIENT_REGISTRATION.REG_DT) = '$Tahun' and RESULT_DATA.result like '%Positif%' and RESULT_DATA.his_reg_no <> ''");
        return $q->result();
    }

    public function HitungJumlah($noLab, $tanggal, $keyword, $keyword1)
    {
        // $this->db_empat = $this->load->database('db4', true);
        $q = $this->db_kedua->query("SELECT COUNT(distinct PasienMasukRumahSakit.NoPendaftaran) AS Jumlah
        FROM         RegistrasiLaboratorium INNER JOIN
                              PasienMasukRumahSakit ON RegistrasiLaboratorium.NoPendaftaran = PasienMasukRumahSakit.NoPendaftaran INNER JOIN
                              Ruangan ON PasienMasukRumahSakit.KdRuangan = Ruangan.KdRuangan INNER JOIN
                              PasienDaftar ON PasienMasukRumahSakit.NoPendaftaran = PasienDaftar.NoPendaftaran INNER JOIN
                              Pasien ON PasienDaftar.NoCM = Pasien.NoCM
        WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ($noLab)) AND (Ruangan.KdInstalasi = '03') AND (PasienMasukRumahSakit.StatusPeriksa = 'Y') AND 
                              (CONVERT(date, PasienMasukRumahSakit.TglMasuk) <= '$tanggal') AND (CONVERT(date, PasienDaftar.TglPulang) > '$tanggal' OR PasienDaftar.TglPulang IS NULL) AND $keyword AND $keyword1");
        return $q->result();
    }

    public function HitungPulangIsolasi($noLab, $bulan, $tahun)
    {
        // $this->db_empat = $this->load->database('db4', true);
        $q = $this->db_kedua->query("Select COUNT(distinct NoPendaftaran) AS Jumlah from (
            SELECT DISTINCT RegistrasiLaboratorium.NoPendaftaran, Case When PasienPulang.KdKondisiPulang IN ('04','05','09','10','11') Then 'Meninggal' When PasienPulang.KdKondisiPulang IN ('08') Then 'DiRujuk' Else 'Sembuh' End AS KondisiPulang
            FROM         RegistrasiLaboratorium INNER JOIN PasienPulang ON RegistrasiLaboratorium.NoPendaftaran = PasienPulang.NoPendaftaran INNER JOIN
            Ruangan ON PasienPulang.KdRuangan = Ruangan.KdRuangan INNER JOIN KondisiPulang ON PasienPulang.KdKondisiPulang = KondisiPulang.KdKondisiPulang
            WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ($noLab) AND (Ruangan.KdInstalasi = '03') AND (Ruangan.NamaRuangan like '%isolasi%') AND 
            (MONTH(PasienPulang.TglPulang) = '$bulan') AND (YEAR(PasienPulang.TglPulang) = '$tahun'))
            ) AS Data ");
        return $q->result();
    }

    public function GetDetailPulangCovid($noLab, $bulan, $tahun)
    {
        // $this->db_empat = $this->load->database('db4', true);
        $q = $this->db_kedua->query("SELECT DISTINCT PasienDaftar.NoPendaftaran, Pasien.NoRM, Pasien.NamaLengkap AS NamaPasien, CONVERT(varchar, Pasien.TglLahir, 105) AS TglLahir, 
        Pasien.JenisKelamin, Pasien.Alamat, CONVERT(varchar, PasienDaftar.TglPendaftaran, 113) AS TglMasuk, CONVERT(varchar, PasienPulang.TglPulang, 113) AS TglPulang, 
        Ruangan.NamaRuangan AS RuangPerawatan, CASE WHEN PasienPulang.KdKondisiPulang IN ('04','05', '09', '10', '11') THEN 'Meninggal' WHEN PasienPulang.KdKondisiPulang IN ('08') 
        THEN 'DiRujuk' ELSE 'Sembuh' END AS KondisiPulang, 'Positif Pemeriksaan dilakukan di Laboratorium rujukan' AS HasilLab, dbo.Ambil_DiagnosaUtama(PasienPulang.NoPendaftaran) AS 
        Diagnosa FROM RegistrasiLaboratorium INNER JOIN PasienPulang ON RegistrasiLaboratorium.NoPendaftaran = PasienPulang.NoPendaftaran INNER JOIN Ruangan ON PasienPulang.KdRuangan = 
        Ruangan.KdRuangan INNER JOIN KondisiPulang ON PasienPulang.KdKondisiPulang = KondisiPulang.KdKondisiPulang INNER JOIN Pasien ON PasienPulang.NoCM = Pasien.NoCM INNER JOIN
        PasienDaftar ON RegistrasiLaboratorium.NoPendaftaran = PasienDaftar.NoPendaftaran WHERE (RegistrasiLaboratorium.NoLaboratorium IN ($noLab)) AND (Ruangan.KdInstalasi = '03') AND 
        (PasienPulang.KdRuangan IN ('222', '304', '312', '012', '014', '017', '018', '013', '209', '601')) AND (MONTH(PasienPulang.TglPulang) = '$bulan') AND
         (YEAR(PasienPulang.TglPulang) = '$tahun')");
        return $q->result();
    }

    public function HitungPulangIntensif($noLab, $bulan, $tahun)
    {
        // $this->db_empat = $this->load->database('db4', true);
        $q = $this->db_kedua->query("Select COUNT(distinct NoPendaftaran) AS Jumlah from (
            SELECT DISTINCT RegistrasiLaboratorium.NoPendaftaran, Case When PasienPulang.KdKondisiPulang IN ('04','05','09','10','11') Then 'Meninggal' When PasienPulang.KdKondisiPulang IN ('08') Then 'DiRujuk' Else 'Sembuh' End AS KondisiPulang
            FROM         RegistrasiLaboratorium INNER JOIN PasienPulang ON RegistrasiLaboratorium.NoPendaftaran = PasienPulang.NoPendaftaran INNER JOIN
            Ruangan ON PasienPulang.KdRuangan = Ruangan.KdRuangan INNER JOIN KondisiPulang ON PasienPulang.KdKondisiPulang = KondisiPulang.KdKondisiPulang
            WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ($noLab) AND (Ruangan.KdInstalasi = '03') AND (PasienPulang.KdRuangan IN ('209','601','016')) AND 
            (MONTH(PasienPulang.TglPulang) = '$bulan') AND (YEAR(PasienPulang.TglPulang) = '$tahun'))) AS Data ");
        return $q->result();
    }

    public function HitungDetailPulangIsolasi($noLab, $bulan, $tahun)
    {
        // $this->db_empat = $this->load->database('db4', true);
        $q = $this->db_kedua->query("Select KondisiPulang, COUNT(distinct NoPendaftaran) AS Jumlah from (
            SELECT DISTINCT RegistrasiLaboratorium.NoPendaftaran, Case When PasienPulang.KdKondisiPulang IN ('04','05','09','10','11') Then 'Meninggal' When PasienPulang.KdKondisiPulang IN ('08') Then 'DiRujuk' Else 'Sembuh' End AS KondisiPulang
            FROM         RegistrasiLaboratorium INNER JOIN PasienPulang ON RegistrasiLaboratorium.NoPendaftaran = PasienPulang.NoPendaftaran INNER JOIN
            Ruangan ON PasienPulang.KdRuangan = Ruangan.KdRuangan INNER JOIN KondisiPulang ON PasienPulang.KdKondisiPulang = KondisiPulang.KdKondisiPulang
            WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ($noLab)) AND (Ruangan.KdInstalasi = '03') AND (Ruangan.NamaRuangan like '%isolasi%') AND 
            (MONTH(PasienPulang.TglPulang) = '$bulan') AND (YEAR(PasienPulang.TglPulang) = '$tahun')) AS Data Group By KondisiPulang");
        return $q->result();
    }

    public function HitungDetailPulangIntensif($noLab, $bulan, $tahun)
    {
        // $this->db_empat = $this->load->database('db4', true);
        $q = $this->db_kedua->query("Select KondisiPulang, COUNT(distinct NoPendaftaran) AS Jumlah from (
            SELECT DISTINCT RegistrasiLaboratorium.NoPendaftaran, Case When PasienPulang.KdKondisiPulang IN ('04','05','09','10','11') Then 'Meninggal' When PasienPulang.KdKondisiPulang IN ('08') Then 'DiRujuk' Else 'Sembuh' End AS KondisiPulang
            FROM         RegistrasiLaboratorium INNER JOIN PasienPulang ON RegistrasiLaboratorium.NoPendaftaran = PasienPulang.NoPendaftaran INNER JOIN
            Ruangan ON PasienPulang.KdRuangan = Ruangan.KdRuangan INNER JOIN KondisiPulang ON PasienPulang.KdKondisiPulang = KondisiPulang.KdKondisiPulang
            WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ($noLab)) AND (Ruangan.KdInstalasi = '03') AND (PasienPulang.KdRuangan IN ('209','601','016')) AND 
            (MONTH(PasienPulang.TglPulang) = '$bulan') AND (YEAR(PasienPulang.TglPulang) = '$tahun')) AS Data Group By KondisiPulang");
        return $q->result();
    }


    public function HitungJumlahMeninggal($noLab, $tanggal, $keyword, $keyword1)
    {
        // $this->db_empat = $this->load->database('db4', true);
        $q = $this->db_kedua->query("SELECT  COUNT(DISTINCT PasienMeninggal.NoPendaftaran) AS Jumlah
        FROM         RegistrasiLaboratorium INNER JOIN
                              PasienMeninggal ON RegistrasiLaboratorium.NoPendaftaran = PasienMeninggal.NoPendaftaran INNER JOIN
                              Ruangan ON PasienMeninggal.KdRuangan = Ruangan.KdRuangan INNER JOIN
                              PasienDaftar ON PasienMeninggal.NoPendaftaran = PasienDaftar.NoPendaftaran INNER JOIN
                              Pasien ON PasienDaftar.NoCM = Pasien.NoCM WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ($noLab)) AND (Ruangan.KdInstalasi = '03') 
                              AND (Convert(date, PasienMeninggal.TglMeninggal) = '$tanggal') AND $keyword AND $keyword1");
        // $q = $this->db_kedua->query("SELECT COUNT(distinct  PasienMeninggal.NoPendaftaran) AS Jumlah
        // FROM         RegistrasiLaboratorium INNER JOIN
        //                       PasienMeninggal ON RegistrasiLaboratorium.NoPendaftaran = PasienMeninggal.NoPendaftaran INNER JOIN
        //                       Ruangan ON PasienMeninggal.KdRuangan = Ruangan.KdRuangan
        // WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ($noLab)) AND (Ruangan.KdInstalasi = '03') AND (Convert(date, PasienMeninggal.TglMeninggal) = '$tanggal')");
        return $q->result();
    }

    public function DetailPasienCovid($noLab, $tanggal, $keyword, $keyword1)
    {
        // $this->db_empat = $this->load->database('db4', true);
        $q = $this->db_kedua->query("SELECT DISTINCT 
        RegistrasiLaboratorium.NoPendaftaran, PasienDaftar.NoCM, PasienDaftar.TglPendaftaran, PasienDaftar.TglPulang, PasienDaftar.KdRuanganAkhir, 
        'Positif Pemeriksaan dilakukan di Laboratorium rujukan' AS HasilLab, dbo.Ambil_DiagnosaUtama(PasienDaftar.NoPendaftaran) AS Diagnosa
        FROM         RegistrasiLaboratorium INNER JOIN
        PasienDaftar ON RegistrasiLaboratorium.NoPendaftaran = PasienDaftar.NoPendaftaran INNER JOIN
        Ruangan ON PasienDaftar.KdRuanganAkhir = Ruangan.KdRuangan INNER JOIN
        Pasien ON PasienDaftar.NoCM = Pasien.NoCM INNER JOIN
        PasienMasukRumahSakit ON PasienDaftar.NoPendaftaran = PasienMasukRumahSakit.NoPendaftaran WHERE (RegistrasiLaboratorium.NoLaboratorium IN ($noLab)) AND (Ruangan.KdInstalasi = '03') 
        AND (PasienMasukRumahSakit.StatusPeriksa = 'Y') AND (CONVERT(date, PasienMasukRumahSakit.TglMasuk) <= '$tanggal') AND (CONVERT(date, PasienDaftar.TglPulang) > '$tanggal' OR PasienDaftar.TglPulang IS NULL) AND $keyword AND $keyword1");
        return $q->result();
    }

    public function DetailPasienCovidMeninggal($noLab, $tanggal, $keyword, $keyword1)
    {
        // $this->db_empat = $this->load->database('db4', true);
        $q = $this->db_kedua->query("SELECT DISTINCT 
        RegistrasiLaboratorium.NoPendaftaran, PasienMeninggal.NoCM, PasienMeninggal.TglMeninggal, PasienMeninggal.KdRuangan, 
        'Positif Pemeriksaan dilakukan di Laboratorium rujukan' AS HasilLab, dbo.Ambil_DiagnosaUtama(PasienMeninggal.NoPendaftaran) AS Diagnosa
        FROM         RegistrasiLaboratorium INNER JOIN
        PasienMeninggal ON RegistrasiLaboratorium.NoPendaftaran = PasienMeninggal.NoPendaftaran INNER JOIN
        Ruangan ON PasienMeninggal.KdRuangan = Ruangan.KdRuangan INNER JOIN
        PasienDaftar ON PasienMeninggal.NoPendaftaran = PasienDaftar.NoPendaftaran INNER JOIN
        Pasien ON PasienDaftar.NoCM = Pasien.NoCM
        WHERE     (RegistrasiLaboratorium.NoLaboratorium IN ($noLab)) AND (Ruangan.KdInstalasi = '03') AND (Convert(date, PasienMeninggal.TglMeninggal) = '$tanggal') AND $keyword AND $keyword1");
        return $q->result();
    }

    public function loadhasil()
    {
        // $this->db_empat = $this->load->database('db4', true);
        $q = $this->db_kedua->query("select Tanggal, Jumlah, JumlahMeninggal, MONTH(Tanggal) AS Bulan, YEAR(Tanggal) AS Tahun from tempCovid order by Tanggal");
        return $q->result();
    }

    public function loadhasilCetak($Bulan, $Tahun)
    {
        // $this->db_empat = $this->load->database('db4', true);
        $q = $this->db_kedua->query("select Tanggal, Jumlah, JumlahMeninggal from tempCovid Where MONTH(Tanggal) = '$Bulan' and YEAR(Tanggal) = '$Tahun' order by Tanggal");
        return $q->result();
    }

    public function detailloadhasil($tanggal)
    {
        // $this->db_empat = $this->load->database('db4', true);

        $q = $this->db_kedua->query("SELECT ROW_NUMBER() OVER (ORDER BY Kode) AS Nomor, DetailPasienCovid.Kode, DetailPasienCovid.NoPendaftaran, Pasien.NoRM, Pasien.NamaLengkap, Ruangan.NamaRuangan AS RuangRawat, DetailPasienCovid.TglPendaftaran, 
        DetailPasienCovid.TglPulang, CONVERT(date, DetailPasienCovid.TanggalFilter) AS TanggalFilter, 'Positif Pemeriksaan dilakukan di Laboratorium rujukan' AS HasilLab, 
        dbo.Ambil_DiagnosaUtama(DetailPasienCovid.NoPendaftaran) AS Diagnosa, KondisiPulang.KondisiPulang FROM KondisiPulang INNER JOIN PasienPulang ON KondisiPulang.KdKondisiPulang = PasienPulang.KdKondisiPulang RIGHT OUTER JOIN
        DetailPasienCovid INNER JOIN Pasien ON DetailPasienCovid.NoCm = Pasien.NoCM INNER JOIN Ruangan ON DetailPasienCovid.KdRuanganAkhir = Ruangan.KdRuangan ON PasienPulang.NoPendaftaran 
        = DetailPasienCovid.NoPendaftaran WHERE (CONVERT(date, DetailPasienCovid.TanggalFilter) = '$tanggal')");
        // $q = $this->db_kedua->query("SELECT ROW_NUMBER() OVER (ORDER BY Kode) AS Nomor, DetailPasienCovid.Kode, DetailPasienCovid.NoPendaftaran, Pasien.NoRM, Pasien.NamaLengkap, Ruangan.NamaRuangan AS RuangRawat, DetailPasienCovid.TglPendaftaran, 
        // DetailPasienCovid.TglPulang, Convert(date, DetailPasienCovid.TanggalFilter) AS TanggalFilter, 'Positif Pemeriksaan dilakukan di Laboratorium rujukan' AS HasilLab, dbo.Ambil_DiagnosaUtama(DetailPasienCovid.NoPendaftaran) AS Diagnosa
        // FROM  DetailPasienCovid INNER JOIN
        // Pasien ON DetailPasienCovid.NoCm = Pasien.NoCM INNER JOIN
        // Ruangan ON DetailPasienCovid.KdRuanganAkhir = Ruangan.KdRuangan
        // WHERE (CONVERT(date, DetailPasienCovid.TanggalFilter) = '$tanggal')");
        return $q->result();
    }

    public function detailloadhasilMeninggal($tanggal)
    {
        // $this->db_empat = $this->load->database('db4', true);
        $q = $this->db_kedua->query("SELECT ROW_NUMBER() OVER (ORDER BY Kode) AS Nomor, DetailPasienMeninggalCovid.Kode, DetailPasienMeninggalCovid.NoPendaftaran, Pasien.NoRM, Pasien.NamaLengkap, Ruangan.NamaRuangan AS RuangRawat, 
        DetailPasienMeninggalCovid.TglMeninggal, CONVERT(date, DetailPasienMeninggalCovid.TglFilter) AS TglFilter, 'Positif Pemeriksaan dilakukan di Laboratorium rujukan' AS HasilLab, 
        dbo.Ambil_DiagnosaUtama(DetailPasienMeninggalCovid.NoPendaftaran) AS Diagnosa, KondisiPulang.KondisiPulang
        FROM KondisiPulang INNER JOIN PasienPulang ON KondisiPulang.KdKondisiPulang = PasienPulang.KdKondisiPulang RIGHT OUTER JOIN
        DetailPasienMeninggalCovid INNER JOIN Pasien ON DetailPasienMeninggalCovid.NoCm = Pasien.NoCM INNER JOIN
        Ruangan ON DetailPasienMeninggalCovid.KdRuangan = Ruangan.KdRuangan ON PasienPulang.NoPendaftaran = DetailPasienMeninggalCovid.NoPendaftaran
        WHERE (CONVERT(date, DetailPasienMeninggalCovid.TglFilter) = '$tanggal')");
        // $q = $this->db_kedua->query("SELECT ROW_NUMBER() OVER (ORDER BY Kode) AS Nomor, DetailPasienMeninggalCovid.Kode, DetailPasienMeninggalCovid.NoPendaftaran, Pasien.NoRM, Pasien.NamaLengkap, Ruangan.NamaRuangan AS RuangRawat, DetailPasienMeninggalCovid.TglMeninggal, Convert(date, DetailPasienMeninggalCovid.TglFilter) AS TglFilter, 'Positif Pemeriksaan dilakukan di Laboratorium rujukan' AS HasilLab, dbo.Ambil_DiagnosaUtama(DetailPasienMeninggalCovid.NoPendaftaran) AS Diagnosa 
        // FROM  DetailPasienMeninggalCovid INNER JOIN
        // Pasien ON DetailPasienMeninggalCovid.NoCm = Pasien.NoCM INNER JOIN
        // Ruangan ON DetailPasienMeninggalCovid.KdRuangan = Ruangan.KdRuangan
        // WHERE (CONVERT(date, DetailPasienMeninggalCovid.TglFilter) = '$tanggal')");
        return $q->result();
    }

    public function GetNoLaboratoriumConfirm($tanggal)
    {

        $this->db_empat = $this->load->database('db4', true);
        $bulan = date('m', strtotime($tanggal));
        $tahun = date('Y', strtotime($tanggal));
        return $this->db_empat->query("select distinct RESULT_DATA.his_reg_no from RESULT_DATA INNER JOIN
        PATIENT_REGISTRATION ON RESULT_DATA.lis_reg_no = PATIENT_REGISTRATION.LIS_REG_NO where RESULT_DATA.HIS_TEST_PARENT_ID = '921941' and Convert(date, PATIENT_REGISTRATION.REG_DT) = '$tanggal' and RESULT_DATA.result like '%Positif%' and RESULT_DATA.his_reg_no <> ''")->result();
        // return $this->db_empat->query("Select PATIENT_MR, (SELECT  top 1    RESULT_DATA.his_reg_no
        // FROM         PATIENT_REGISTRATION INNER JOIN
        //                       RESULT_DATA ON PATIENT_REGISTRATION.LIS_REG_NO = RESULT_DATA.lis_reg_no
        // WHERE     (PATIENT_REGISTRATION.PATIENT_MR = Data.PATIENT_MR) AND (MONTH(PATIENT_REGISTRATION.REG_DT) = '$bulan') AND (YEAR(PATIENT_REGISTRATION.REG_DT) = '$tahun') and (RESULT_DATA.HIS_TEST_PARENT_ID = '921941') order by  test_auth_date ) AS his_reg_no from (
        // SELECT DISTINCT PATIENT_REGISTRATION.PATIENT_MR
        // FROM         RESULT_DATA INNER JOIN
        //                       PATIENT_REGISTRATION ON RESULT_DATA.lis_reg_no = PATIENT_REGISTRATION.LIS_REG_NO
        // WHERE     (RESULT_DATA.HIS_TEST_PARENT_ID = '921941') AND (MONTH(PATIENT_REGISTRATION.REG_DT) = '$bulan') AND (YEAR(PATIENT_REGISTRATION.REG_DT) = '$tahun') AND (RESULT_DATA.result LIKE '%positif%') 
        //                       AND (RESULT_DATA.his_reg_no <> '') 
        //                       ) AS Data order by PATIENT_MR")->result();

        // return $this->db_empat->query("Select PATIENT_MR, (SELECT  top 1    RESULT_DATA.his_reg_no
        // FROM         PATIENT_REGISTRATION INNER JOIN
        //                       RESULT_DATA ON PATIENT_REGISTRATION.LIS_REG_NO = RESULT_DATA.lis_reg_no
        // WHERE     (PATIENT_REGISTRATION.PATIENT_MR = Data.PATIENT_MR) AND (Convert(date, PATIENT_REGISTRATION.REG_DT) = '$tanggal') and (RESULT_DATA.HIS_TEST_PARENT_ID = '921941') order by  test_auth_date ) AS his_reg_no from (
        // SELECT DISTINCT PATIENT_REGISTRATION.PATIENT_MR
        // FROM         RESULT_DATA INNER JOIN
        //                       PATIENT_REGISTRATION ON RESULT_DATA.lis_reg_no = PATIENT_REGISTRATION.LIS_REG_NO
        // WHERE     (RESULT_DATA.HIS_TEST_PARENT_ID = '921941') AND (CONVERT(date, PATIENT_REGISTRATION.REG_DT) = '$tanggal') AND (RESULT_DATA.result LIKE '%positif%') 
        //                       AND (RESULT_DATA.his_reg_no <> '') 
        //                       ) AS Data order by PATIENT_MR");

        // return $this->db_empat->query("SELECT DISTINCT top 10 REG_NUM FROM PATIENT_REGISTRATION");
    }

    public function GetPrilaku($IdPegawai, $Bulan, $Tahun)
    {
        return $this->db_kedua->query("SELECT DataPegawai.IdPegawai, DataPegawai.NamaLengkap, DataCurrentPegawai.nipbaru, DataCurrentPegawai.NoHandkey, 
        Jabatan.NamaJabatan, RuangKerja.RuangKerja, (Select top 1 quesioner1 + quesioner2 + quesioner3 + quesioner4 + quesioner5 + quesioner6 + quesioner7 + 
        quesioner8 + quesioner9 + quesioner10 + quesioner11 +	quesioner12 + quesioner13 + quesioner14 + quesioner15 + quesioner16 + quesioner17 + quesioner18 + 
        quesioner19 + quesioner20 + quesioner21 + quesioner22 + quesioner23 + quesioner24 + quesioner25 from PenilaianPrilakuPegawai where IdPegawai = 
        DataPegawai.IdPegawai And MONTH(TanggalPrilaku)  = '$Bulan' and YEAR(TanggalPrilaku) = '$Tahun' order by TanggalPenilaian desc) As 
        TotalNilai FROM DataPegawai INNER JOIN DataCurrentPegawai ON DataPegawai.IdPegawai = DataCurrentPegawai.IdPegawai INNER JOIN 	 Jabatan ON 
        DataCurrentPegawai.KdJabatan = Jabatan.KdJabatan  INNER JOIN RuangKerja ON DataCurrentPegawai.KdRuanganKerja = RuangKerja.KdRuangKerja where 
        DataPegawai.IdPegawai = '$IdPegawai'")->row_array();
    }

    public function GetAbsenPegawai($IdPegawai, $Bulan, $Tahun, $TanggalAwal, $TanggalAkhir)
    {
        return $this->db_kedua->query("SELECT idPegawai, NamaLengkap,NamaJabatan,typepegawai , SUM( SAKIT ) AS SAKIT, SUM( IJIN ) AS IJIN, SUM( ALPA ) AS ALPA, 
        SUM(DIKLAT) AS DIKLAT,SUM( CUTI ) AS CUTI,SUM(A3.CUTIBERSALIN) AS CUTIBERSALIN,SUM(A3.CUTIBERSALIN3) AS CUTIBERSALIN3,SUM(A3.CUTIALASANPENTING) AS 
        CUTIALASANPENTING,SUM(A3.DINASLUARPENUH) AS DINASLUARPENUH,SUM(TUGASBELAJAR) AS TUGASBELAJAR, SUM(PCB) AS PCB, SUM( PC ) AS PC,SUM(TELAT) AS TELAT, 
        SUM( LIBUR ) AS LIBUR, SUM( HADIR ) AS HADIR,SUM(LUPAABSEN) AS LUPAABSEN,SUM(DINASLUAR) AS DINASLUAR,SUM(JARITDKTERBACA) AS JARITDKTERBACA, 
        SUM( SAKIT ) + SUM( IJIN ) + SUM( ALPA ) + SUM( CUTI ) + SUM( HADIR )+SUM(A3.CUTIBERSALIN) +SUM(A3.CUTIBERSALIN3)+SUM(A3.CUTIALASANPENTING)+
        SUM(A3.DINASLUARPENUH) AS [HRI KRJ], nohandkey, RuangKerja, KdRuangan FROM ( SELECT a2.IdPegawai,a2.NamaLengkap,a2.JadwalKerja, 
        CASE WHEN a2.kodeWaktuKerja = 'SK' THEN 1 ELSE 0 END AS 'SAKIT', CASE WHEN a2.kodeWaktuKerja = 'I' THEN 1 ELSE 0 END AS 'IJIN', 
        CASE WHEN (a2.JamAwal IS NULL AND A2.JAMAKHIR IS NULL AND a2.JadwalMasuk< >'00:00:00.000' AND a2.JadwalKeluar<>'00:00:00.000' OR a2.kodeWaktuKerja='A' ) 
        THEN 1 ELSE 0 END AS 'ALPA', CASE WHEN a2.kodeWaktuKerja = 'DK' THEN 1 ELSE 0 END AS 'DIKLAT', CASE WHEN a2.kodeWaktuKerja = 'CT' THEN 1 ELSE 0 END AS 'CUTI', 
        CASE WHEN a2.kodeWaktuKerja = 'CBS' THEN 1 ELSE 0 END AS 'CUTIBERSALIN', CASE WHEN a2.kodeWaktuKerja = 'PCB' THEN 1 ELSE 0 END AS 'PCB', 
        CASE WHEN a2.kodeWaktuKerja = 'CBS3' THEN 1 ELSE 0 END AS 'CUTIBERSALIN3', CASE WHEN a2.kodeWaktuKerja = 'CAP' THEN 1 ELSE 0 END AS 'CUTIALASANPENTING', 
        CASE WHEN a2.kodeWaktuKerja = 'DLP' THEN 1 ELSE 0 END AS 'DINASLUARPENUH', CASE WHEN left(a2.Ket,10)='Dinas Luar' THEN 1 ELSE 0 END AS 'DINASLUAR', 
        CASE WHEN left(a2.Ket,10)='Jari Tdk T' THEN 1 ELSE 0 END AS 'JARITDKTERBACA', CASE WHEN a2.kodeWaktuKerja = 'TB' THEN 1 ELSE 0 END AS 'TUGASBELAJAR', 
        CASE WHEN a2.pulangcepat<>0 THEN pulangcepat ELSE 0 END AS 'PC', CASE WHEN a2.TELAT<>0 THEN telat ELSE 0 END AS 'TELAT', 
        CASE WHEN left(a2.Ket,10)='Lupa Absen' THEN 1 ELSE 0 END AS 'LUPAABSEN', CASE WHEN a2.JadwalMasuk='00:00:00.000' AND a2.JadwalKeluar='00:00:00.000' 
        AND a2.kodeWaktuKerja NOT IN ('SK','I','CT','CBS','CBS3','TB','DK','OC','DLP') THEN 1 ELSE 0 END AS 'LIBUR', 
        CASE WHEN a2.JadwalMasuk<>'00:00:00.000' AND a2.JadwalKeluar<>'00:00:00.000' AND (a2.JamAwal IS NOT NULL OR a2.JamAkhir IS NOT NULL) THEN 1 
        ELSE 0 END AS 'HADIR', a2.ruangkerja ,a2.KdRuangan,a2.kodeWaktuKerja,a2.nohandkey ,NamaJabatan,typepegawai FROM ( 
            SELECT IdPegawai, nohandkey, NamaLengkap, JadwalKerja, kodeWaktuKerja, JadwalMasuk, JadwalKeluar, 
            case when (dbo.Ambil_KetJadwalKerja(idpegawai,jadwalkerja,kdruangan,kodewaktu)='0') AND convert(int,Telat)>0 then ket else JamAwal END AS JamAwal, 
            case when (dbo.Ambil_KetJadwalKerja(idpegawai,jadwalkerja,kdruangan,kodewaktu)='1') AND convert(int,PulangCepat)<0 then ket else JamAkhir END AS JamAkhir, 
            case when (dbo.Ambil_KetJadwalKerja(idpegawai,jadwalkerja,kdruangan,kodewaktu)='0') AND convert(int,Telat)>0 THEN 0 ELSE convert(int,Telat) END as telat,
            case when (dbo.Ambil_KetJadwalKerja(idpegawai,jadwalkerja,kdruangan,kodewaktu)='1') AND convert(int,PulangCepat)<0 THEN 0 else convert(int,PulangCepat) END as pulangcepat,
            convert(int,ET) as et, convert(int,MasaKerja) as masakerja, case when jamawal is null and jamakhir is null then 0 else convert(int,masakerja) -
            case when (dbo.Ambil_KetJadwalKerja(idpegawai,jadwalkerja,kdruangan,kodewaktu)='0') AND convert(int,Telat)>0 THEN 0 eLSE convert(int,Telat) END+
            case when (dbo.Ambil_KetJadwalKerja(idpegawai,jadwalkerja,kdruangan,kodewaktu)='1') AND convert(int,PulangCepat)<0 THEN 0 else convert(int,PulangCepat) 
            END end AS MasaEfektif,case when jamawal is null and jamakhir is null then 0 else convert(int,masakerja)-case when 
            (dbo.Ambil_KetJadwalKerja(idpegawai,jadwalkerja,kdruangan,kodewaktu)='0') AND convert(int,Telat)>0 THEN 0 ELSE convert(int,Telat) END+
            case when (dbo.Ambil_KetJadwalKerja(idpegawai,jadwalkerja,kdruangan,kodewaktu)='1') AND convert(int,PulangCepat)<0 THEN 0 else convert(int,PulangCepat) 
            END+convert(int,et) end AS MasaEfektif2,ruangkerja ,a.Ket,a.KdRuangan ,NamaJabatan,typepegawai FROM (seLECT dp.IdPegawai, ISNULL(RTRIM(LTRIM(
                case when dcp.NRK='' THEN NULL ELSE dcp.nrk end)), dcp.NoHandkey) AS nohandkey,dp.NamaLengkap, jkb.JadwalKerja, jkb.kodeWaktuKerja, 
                twk.JadwalMasuk, twk.JadwalKeluar,dbo.GetJadwalAbsen(ISNULL(RTRIM(LTRIM(case when dcp.NRK='' THEN NULL ELSE dcp.nrk end)), dcp.NoHandkey) ,'1', 
                jkb.JadwalKerja , twk.JadwalMasuk, twk.JadwalKeluar) AS JamAwal, dbo.GetJadwalAbsen(ISNULL(RTRIM(LTRIM(case when dcp.NRK='' THEN NULL ELSE dcp.nrk end)),
                 dcp.NoHandkey), '2', jkb.JadwalKerja, twk.JadwalMasuk, twk.JadwalKeluar) AS JamAkhir, 
                 dbo.GetJadwalAbsen(ISNULL(RTRIM(LTRIM(case when dcp.NRK='' THEN NULL ELSE dcp.nrk end)), dcp.NoHandkey), '3', jkb.JadwalKerja, 
                 twk.JadwalMasuk, twk.JadwalKeluar) AS Telat, dbo.GetJadwalAbsen(ISNULL(RTRIM(LTRIM(case when dcp.NRK='' THEN NULL ELSE dcp.nrk end)), dcp.NoHandkey), 
                 '4', jkb.JadwalKerja, twk.JadwalMasuk, twk.JadwalKeluar) AS PulangCepat, 
                 dbo.GetJadwalAbsen(ISNULL(RTRIM(LTRIM(case when dcp.NRK='' THEN NULL ELSE dcp.nrk end)), dcp.NoHandkey), '5', jkb.JadwalKerja, 
                 twk.JadwalMasuk, twk.JadwalKeluar) AS ET, dbo.GetJadwalAbsen(ISNULL(RTRIM(LTRIM(case when dcp.NRK='' THEN NULL ELSE dcp.nrk end)), dcp.NoHandkey), '6', 
                 jkb.JadwalKerja, twk.JadwalMasuk, twk.JadwalKeluar) AS MasaKerja, jkb.KdRuangan,ruangkerja ,jkb.Ket,jkb.Keterangan AS kodewaktu ,
                 j.NamaJabatan,tp.TypePegawai,dcp.KdTypePegawai fROM JadwalKerjaBaru AS jkb INNER JOIN DataPegawai AS dp ON dp.IdPegawai = jkb.idPegawai 
                 INNER JOIN DataCurrentPegawai AS dcp ON dp.IdPegawai = dcp.IdPegawai INNER JOIN TblWaktuKerja AS twk ON twk.KodeWaktuKerja = jkb.kodeWaktuKerja 
                 INNER JOIN ruangkerja rk ON rk.KdRuangKerja = jkb.KdRuangan LEFT OUTER JOIN Jabatan j ON j.KdJabatan = dcp.KdJabatan LEFT OUTER JOIN TypePegawai tp 
                 ON tp.KdTypePegawai = dcp.KdTypePegawai WHERE DAY(JadwalKerja) Between '$TanggalAwal' and '$TanggalAkhir' AND MONTH(JadwalKerja) = '$Bulan' AND YEAR(JadwalKerja) = '$Tahun' 
                 AND dcp.IdPegawai = '$IdPegawai' AND jkb.Keterangan='R' ) AS a ) AS a2 ) AS A3 GROUP BY idPegawai, NamaLengkap, nohandkey, RuangKerja, 
                 KdRuangan,NamaJabatan,typepegawai ORDER BY RUANGKERJA,NAMALENGKAP")->row_array();
    }


    function getJadwalOPhariini($tgl, $IdDokter)
    {
        $q = $this->db_kedua->query("SELECT  distinct V_4DaftarOperasi.KdProgram, V_4DaftarOperasi.NoPendaftaran, V_4DaftarOperasi.NamaPasien, V_4DaftarOperasi.Usia, V_4DaftarOperasi.NoRM, V_4DaftarOperasi.NamaDiagnosa AS Diagnosa, 
		V_4DaftarOperasi.Tindakan, V_4DaftarOperasi.DokterOperator, V_4DaftarOperasi.THT, V_4DaftarOperasi.BedahUmum, V_4DaftarOperasi.Kebidanan, V_4DaftarOperasi.Mata, 
		V_4DaftarOperasi.BedahUrologi, V_4DaftarOperasi.BedahOrtopedi, V_4DaftarOperasi.BedahKulit, V_4DaftarOperasi.BedahSyaraf, V_4DaftarOperasi.BedahGIMUL, 
		V_4DaftarOperasi.BedahKardiologi, V_4DaftarOperasi.BedahParu, CONVERT(varchar, V_4DaftarOperasi.TglPengajuanOperasi, 105) AS TglPengajuanOperasi, CONVERT(varchar, 
		V_4DaftarOperasi.TglOperasi, 105) AS TglOperasi, CASE WHEN TglOperasi IS NULL AND TglSelesai IS NULL THEN 'Persiapan Operasi' WHEN TglOperasi IS NOT NULL AND TglSelesai IS NULL 
		THEN 'Operasi Sedang Berlangsung' WHEN TglOperasi IS NOT NULL AND TglSelesai IS NOT NULL THEN 'Operasi Selesai' END AS Status, V_4DaftarOperasi.RuanganPengirim, 
		V_4DaftarOperasi.JenisOperasi, CASE WHEN TglOperasi IS NULL AND TglSelesai IS NULL THEN 'blue' WHEN TglOperasi IS NOT NULL AND TglSelesai IS NULL 
		THEN 'red' WHEN TglOperasi IS NOT NULL AND TglSelesai IS NOT NULL THEN 'green' END AS StatusWarna, CONVERT(varchar, DATEDIFF(MINUTE, V_4DaftarOperasi.TglOperasi, 
		V_4DaftarOperasi.TglSelesai) / 60) + ' Jam ' + CONVERT(varchar, DATEDIFF(MINUTE, V_4DaftarOperasi.TglOperasi, V_4DaftarOperasi.TglSelesai) % 60) + ' Menit' AS WaktuTunggu, 
		V_4DaftarOperasi.KdJenisOperasi, V_4DaftarOperasi.Diagnosa AS KdDiagnosa, V_4DaftarOperasi.IdDokter, V_4DaftarOperasi.KdRuanganAsal, V_4DaftarOperasi.Keterangan, Case When DataPegawai.Nama2 IS NULL Then DataPegawai.NamaLengkap Else DataPegawai.Nama2 End AS DokterOP2, registerMasukKamarOperasi.idDokterOP2
		FROM DataPegawai INNER JOIN registerMasukKamarOperasi ON DataPegawai.IdPegawai = registerMasukKamarOperasi.idDokterOP2 RIGHT OUTER JOIN
        V_4DaftarOperasi ON registerMasukKamarOperasi.KdProgram = V_4DaftarOperasi.KdProgram
		WHERE (CONVERT(date, V_4DaftarOperasi.TglPengajuanOperasi) = '$tgl') AND (IdDokter = '$IdDokter')");
        return $q->result();
    }

    public function GetemailDokter($tanggal)
    {
        return $this->db_kedua->query("SELECT IdDokter, Nama, Email
        FROM         (SELECT DISTINCT ProgramOperasiRS.IdDokter, CASE WHEN DataPegawai.Nama2 IS NULL THEN DataPegawai.NamaLengkap ELSE DataPegawai.Nama2 END AS Nama,
                                                          (SELECT     TOP (1) Email
                                                            FROM          DataAlamatPegawai
                                                            WHERE      (IdPegawai = ProgramOperasiRS.IdDokter) AND (Email NOT IN ('', '-'))) AS Email
                               FROM          ProgramOperasiRS INNER JOIN
                                                      DataPegawai ON ProgramOperasiRS.IdDokter = DataPegawai.IdPegawai
                               WHERE      (CONVERT(date, ProgramOperasiRS.TglRencana) = '$tanggal')) AS Data
        WHERE     (Email IS NOT NULL)")->result();
    }

    public function GetSertifikat($kdwebinar)
    {
        return $this->db->query("SELECT registerwebinar.id, registerwebinar.nama, registerwebinar.email, absenwebinar.tanggal
        FROM         registerwebinar INNER JOIN
                              absenwebinar ON registerwebinar.KodeWebinar = absenwebinar.KdWebinar AND registerwebinar.email = absenwebinar.email
        WHERE     (registerwebinar.KodeWebinar = '$kdwebinar')")->result();
    }

    public function CekHologramPeserta($kdregister)
    {
        return $this->db->query("select COUNT(id) AS Jumlah from hologramPeserta where kdRegister = '$kdregister'")->result();
    }

    public function GetUrutHologram()
    {
        return $this->db->query("select COUNT(id) AS Jumlah from hologramPeserta")->result();
    }

    public function GetWebinar($kdwebinar)
    {
        return $this->db->query("SELECT id, KodeWebinar, email, date, via, nama, jenispeserta FROM registerwebinar where KodeWebinar = '$kdwebinar'")->result();
    }

    public function GetKinerjaPegawai($IdPegawai, $Bulan, $Tahun)
    {
        return $this->db_kedua->query("Select * from V_4CapaianKinerja Where IdPegawai = '$IdPegawai' and MONTH(Tanggal2) = '$Bulan' and
              YEAR(Tanggal2) = '$Tahun' Order By TanggalInput desc")->result();
    }

    public function CekToken($email, $password)
    {
        return $this->db->query("select * from user_token where email = '$email' and token = '$password' and DATEDIFF(MINUTE, date_created, GETDATE()) <= 30")->result();
    }

    public function GetPertanyaanSkrining($id, $urut)
    {
        if ($id == '1') {
            return $this->db_kedua->query("select Kode, Pertannyaan, Urutan from MasterSkrining where Kode IN ('1','2','3','4') and Urutan = '$urut' Order By Urutan")->result();
        } else {
            return $this->db_kedua->query("select Kode, Pertannyaan, Urutan from MasterSkrining where Urutan = '$urut' Order By Urutan")->result();
        }
    }

    public function GetAllCapaian($IdPegawai, $Bulan, $Tahun)
    {
        return $this->db_kedua->query("Select SUM(waktu * Jumlah) AS Jumlah, SUM(Capaian) AS Capaian from V_4CapaianKinerjaAll Where IdPegawai = '$IdPegawai' AND MONTH(Tanggal) = '$Bulan' AND year(Tanggal) = '$Tahun'")->row_array();
    }

    public function GetPenambahanCapaianWaktuEfektif($IdPegawai, $Bulan, $Tahun)
    {
        return $this->db_kedua->query("EXEC SP_PenambahWaktuEfektif '$IdPegawai', '$Bulan', '$Tahun'")->result();
    }

    public function menu()
    {
        $query = "SELECT * from user_menuNew";
        return $this->db->query($query)->result_array();
    }

    public function getAccessMenu()
    {
        $query = "SELECT user_access_menu.id, user_access_menu.role_id, user_access_menu.menu_id, user_role.role, user_menuNew.menu
        FROM user_access_menu INNER JOIN user_role ON user_access_menu.role_id = user_role.id INNER JOIN user_menuNew ON user_access_menu.menu_id = user_menuNew.id order by user_role.role, user_menuNew.menu";
        return $this->db->query($query)->result_array();
    }


    public function dataagama()
    {
        $this->db_kedua = $this->load->database('db2', true);
        $query = "Select * from Agama";
        return $this->db_kedua->query($query)->result_array();
    }

    function getUsers($postData)
    {

        $response = array();

        if (isset($postData['search'])) {
            // Select record
            $this->db_kedua->select("Kode, NamaAktifitas + ' -' + Convert(varchar, Waktu) + ' Menit' AS NamaAktifitas");
            $this->db_kedua->where("NamaAktifitas like '%" . $postData['search'] . "%' ");
            $this->db_kedua->limit(20);

            $records = $this->db_kedua->get('Aktifitas')->result();

            foreach ($records as $row) {
                $response[] = array("value" => $row->Kode, "label" => $row->NamaAktifitas);
            }
        }

        return $response;
    }

    public function view()
    {
        return $this->db->get('user_sub_menu')->result(); // Tampilkan semua data yang ada di tabel siswa
    }

    public function datapegawai()
    {
        $this->db_kedua = $this->load->database('db2', true);
        $query = "Select top 2 idpegawai, namalengkap from Datapegawai order by idpegawai";
        return $this->db_kedua->query($query)->result_array();
    }
    public function dataremun()
    {
        $this->db_ketiga = $this->load->database('db3', true);
        $idpegawai = $this->session->userdata('idpegawai');
        $pilihtahun = $this->input->post('pilihtahun');
        // $query = "SELECT r.IdPegawai, r.TglBayar, r.NamaPegawai, r.Bulan, r.Tahun, r.Jabatan, r.TotalRemun, r.Kelebihan, 
        // r.Kekurangan, r.SimpananWajib, r.Koperasi, r.SimpananPokok, r.BAMC, r.Obat, r.UangDuka, r.Infaq, r.Rokris,r.Tombokan, 
        // r.Futsal,r.Lainlain, isnull(mprpa.Pph,0) as Pph,  r.TotalNett - isnull(Pph,0) TotalNett, r.NoRekening, r.RuangKerja, r.TipePegawai, 
        // r.KdGolongan, r.NPWP  from v_remon r   LEFT OUTER JOIN ManualPajak_RekapPendapatanAll mprpa ON mprpa.TglBayar = r.TglBayar 
        // AND mprpa.IdPegawai = r.IdPegawai AND mprpa.KdJenisPendapatan = 6 where r.idpegawai = '$idpegawai'  and year(r.TglBayar) = '$pilihtahun'";

        $query = "SELECT * from (
            SELECT r.IdPegawai, r.TglBayar, r.NamaPegawai, r.Bulan, r.Tahun, r.Jabatan, r.TotalRemun, r.Kelebihan, 
                            r.Kekurangan, r.SimpananWajib, r.Koperasi, r.SimpananPokok, r.BAMC, r.Obat, r.UangDuka, r.Infaq, r.Rokris,r.Tombokan, 
                            r.Futsal,r.Lainlain, isnull(mprpa.Pph,0) as Pph,  r.TotalNett - isnull(Pph,0) TotalNett, r.NoRekening, r.RuangKerja, r.TipePegawai, 
                            r.KdGolongan, r.NPWP, r.JabatanUtama, r.JabatanRangkap  from v_remonx r   LEFT OUTER JOIN ManualPajak_RekapPendapatanAll mprpa ON mprpa.TglBayar = r.TglBayar 
                            AND mprpa.IdPegawai = r.IdPegawai AND mprpa.KdJenisPendapatan = 6 where r.idpegawai = '$idpegawai'  and year(r.TglBayar) = '$pilihtahun'
            
                             union all
                    
                    SELECT        IdPegawai, TglBayar, NamaPegawai, Bln, Thn, NamaJabatan, NominalBruto, Kelebihan, Kekurangan, SimpananWajib, Koperasi, SimpananPokok, BAMC, Obat, UangDuka, Infaq, Rokris, Tombokan, Futsal, LainLain, pph, Nominalbruto-potongan- isnull(Pph,0) TotalNett,  
                                             NoRekening, RuangKerja, TipePegawai, KdGolongan, NPWP, JabatanUtama, JabatanRangkap
                    FROM            view_remun AS r
                    WHERE        (IdPegawai = '$idpegawai') AND (YEAR(TglBayar) = '$pilihtahun')) as data INNER JOIN PostingWeb pw on pw.Bulan = MONTH(data.TglBayar) and pw.Tahun = YEAR(data.TglBayar)";



        //         SELECT NamaPegawai,Bulan,Tahun,Jabatan,
        //         convert(int,TotalRemun)as TotalRemun,convert(int,Kelebihan)as Kelebihan,convert(int,Kekurangan) as Kekurangan,convert(int,SimpananWajib) as SimpananWajib,
        // convert(int,Koperasi)as Koperasi,convert(int,SimpananPokok)as SimpananPokok, convert(int,BAMC)as BAMC,convert(int,Obat) as Obat,convert(int,UangDuka) as UangDuka,
        // convert(int,Infaq)as Infaq,convert(int,Rokris)as Rokris,convert(int,Tombokan)as Tombokan,convert(int,Futsal) as Futsal,convert(int,PPH21) as PPH21,convert(int,Lainlain)as Lainlain,
        // convert(int,TotalNett) as TotalNett from V_Remon where idpegawai = '$idpegawai'  and Tahun = '$pilihtahun'
        return $this->db_ketiga->query($query)->result_array();
    }
    public function slipremun()
    {
        $this->db_ketiga = $this->load->database('db3', true);
        $idpegawai = $this->session->userdata('idpegawai');
        $pilihtahun = $this->input->post('pilihtahun');
        $bln =  $this->input->get('bln');
        $thn =  $this->input->get('thn');


        $query = "SELECT r.IdPegawai, r.TglBayar, r.NamaPegawai, r.Bulan, r.Tahun, r.Jabatan, r.TotalRemun, r.Kelebihan, 
        r.Kekurangan, r.SimpananWajib, r.Koperasi, r.SimpananPokok, r.BAMC, r.Obat, r.UangDuka, r.Infaq, r.Rokris,r.Tombokan, 
        r.Futsal,r.Lainlain, isnull(mprpa.Pph,0) as Pph,  r.TotalNett - isnull(Pph,0) TotalNett, r.NoRekening, r.RuangKerja, r.TipePegawai, 
        r.KdGolongan, r.NPWP, r.JabatanUtama, r.JabatanRangkap  from v_remonx r   LEFT OUTER JOIN ManualPajak_RekapPendapatanAll mprpa ON mprpa.TglBayar = r.TglBayar 
        AND mprpa.IdPegawai = r.IdPegawai AND mprpa.KdJenisPendapatan = 6 where r.idpegawai = '$idpegawai'  and year(r.TglBayar) = '$thn' and MONTH(r.TglBayar) = '$bln'
        union all
        
        SELECT        IdPegawai, TglBayar, NamaPegawai, Bln, Thn, NamaJabatan, NominalBruto, Kelebihan, Kekurangan, SimpananWajib, Koperasi, SimpananPokok, BAMC, Obat, UangDuka, Infaq, Rokris, Tombokan, Futsal, LainLain, pph, Nominalbruto-potongan- isnull(Pph,0) TotalNett,  
                                 NoRekening, RuangKerja, TipePegawai, KdGolongan, NPWP, JabatanUtama, JabatanRangkap
        FROM            view_remun AS r
        WHERE        (IdPegawai = '$idpegawai') AND (YEAR(TglBayar) = '$thn') AND (MONTH(TglBayar) = '$bln')";
        //         SELECT NamaPegawai,Bulan,Tahun,Jabatan,
        //         convert(int,TotalRemun)as TotalRemun,convert(int,Kelebihan)as Kelebihan,convert(int,Kekurangan) as Kekurangan,convert(int,SimpananWajib) as SimpananWajib,
        // convert(int,Koperasi)as Koperasi,convert(int,SimpananPokok)as SimpananPokok, convert(int,BAMC)as BAMC,convert(int,Obat) as Obat,convert(int,UangDuka) as UangDuka,
        // convert(int,Infaq)as Infaq,convert(int,Rokris)as Rokris,convert(int,Tombokan)as Tombokan,convert(int,Futsal) as Futsal,convert(int,PPH21) as PPH21,convert(int,Lainlain)as Lainlain,
        // convert(int,TotalNett) as TotalNett from V_Remon where idpegawai = '$idpegawai'  and Tahun = '$pilihtahun'
        return $this->db_ketiga->query($query)->row();
    }
    public function jasazambrud()
    {
        $this->db_ketiga = $this->load->database('db3', true);
        $idpegawai = $this->session->userdata('idpegawai');
        $pilihtahun = $this->input->post('pilihtahun');
        $bln =  $this->input->get('bln');
        $thn =  $this->input->get('thn');
        $query = "SELECT   rpa.TglBayar as Tanggal,     rpa.NominalBruto  ,  'JasaDokter' as Judul,rpa.idpegawai, 
        RuangKerja,NamaPegawai,npwp nonpwp,norekening,mprpa.pph FROM  ManualPajak_RekapPendapatanAll AS rpa 
        INNER JOIN MasterPegawai mdpn ON rpa.idpegawai = mdpn.idpegawai  LEFT OUTER JOIN ManualPajak_RekapPendapatanAll mprpa 
        ON mprpa.TglBayar = rpa.TglBayar AND mprpa.IdPegawai = rpa.IdPegawai AND mprpa.KdJenisPendapatan = rpa.KdJenisPendapatan AND 
        mprpa.TglKegiatan = rpa.TglKegiatan AND mprpa.KdWaktu = rpa.KdWaktu WHERE   year(rpa.tglbayar) = '$thn' AND   month(rpa.tglbayar) = '$bln' 
        and rpa.kdjenispendapatan='16' and rpa.idpegawai='$idpegawai'";
        //         SELECT NamaPegawai,Bulan,Tahun,Jabatan,
        //         convert(int,TotalRemun)as TotalRemun,convert(int,Kelebihan)as Kelebihan,convert(int,Kekurangan) as Kekurangan,convert(int,SimpananWajib) as SimpananWajib,
        // convert(int,Koperasi)as Koperasi,convert(int,SimpananPokok)as SimpananPokok, convert(int,BAMC)as BAMC,convert(int,Obat) as Obat,convert(int,UangDuka) as UangDuka,
        // convert(int,Infaq)as Infaq,convert(int,Rokris)as Rokris,convert(int,Tombokan)as Tombokan,convert(int,Futsal) as Futsal,convert(int,PPH21) as PPH21,convert(int,Lainlain)as Lainlain,
        // convert(int,TotalNett) as TotalNett from V_Remon where idpegawai = '$idpegawai'  and Tahun = '$pilihtahun'
        return $this->db_ketiga->query($query)->row();
    }
    public function jasadokter()
    {
        $this->db_ketiga = $this->load->database('db3', true);
        $idpegawai = $this->session->userdata('idpegawai');
        $pilihtahun = $this->input->post('pilihtahun');
        $bln =  $this->input->get('bln');
        $thn =  $this->input->get('thn');
        $query = "SELECT   rpa.TglBayar as Tanggal,     rpa.NominalBruto  ,  'JasaDokter' as Judul,rpa.idpegawai, RuangKerja,NamaPegawai,npwp nonpwp,norekening,isnull(mprpa.pph,0) pph FROM  ManualPajak_RekapPendapatanAll AS rpa INNER JOIN MasterPegawai mdpn ON rpa.idpegawai = mdpn.idpegawai 
        LEFT OUTER JOIN ManualPajak_RekapPendapatanAll mprpa ON mprpa.TglBayar = rpa.TglBayar AND mprpa.IdPegawai = rpa.IdPegawai AND mprpa.KdJenisPendapatan = rpa.KdJenisPendapatan AND mprpa.TglKegiatan = rpa.TglKegiatan AND mprpa.KdWaktu = rpa.KdWaktu 
        WHERE   year(rpa.tglbayar) ='$thn'AND   month(rpa.tglbayar) ='$bln' and rpa.kdjenispendapatan='14'  and rpa.idpegawai='$idpegawai'";
        //         SELECT NamaPegawai,Bulan,Tahun,Jabatan,
        //         convert(int,TotalRemun)as TotalRemun,convert(int,Kelebihan)as Kelebihan,convert(int,Kekurangan) as Kekurangan,convert(int,SimpananWajib) as SimpananWajib,
        // convert(int,Koperasi)as Koperasi,convert(int,SimpananPokok)as SimpananPokok, convert(int,BAMC)as BAMC,convert(int,Obat) as Obat,convert(int,UangDuka) as UangDuka,
        // convert(int,Infaq)as Infaq,convert(int,Rokris)as Rokris,convert(int,Tombokan)as Tombokan,convert(int,Futsal) as Futsal,convert(int,PPH21) as PPH21,convert(int,Lainlain)as Lainlain,
        // convert(int,TotalNett) as TotalNett from V_Remon where idpegawai = '$idpegawai'  and Tahun = '$pilihtahun'
        return $this->db_ketiga->query($query)->row();
    }

    public function tunradiasi()
    {
        $this->db_ketiga = $this->load->database('db3', true);
        $idpegawai = $this->session->userdata('idpegawai');
        $pilihtahun = $this->input->post('pilihtahun');
        $bln =  $this->input->get('bln');
        $thn =  $this->input->get('thn');
        $query = "SELECT *,0 as gaji,0 as jagut,0 as jasadokter,0 as nerus,0 as oncall,0 as remunerasi,0 as shift,'15'+'/'+cast(bln as varchar(5))+'/'+cast(thn as varchar(5)) as Tanggal 
         from V_ListTunjanganRadiasi_Posting where month(tglbayar)=   '$bln' AND year(tglbayar)=   '$thn' and kdjenispendapatan='8' and idpegawai='$idpegawai' ";
        //         SELECT NamaPegawai,Bulan,Tahun,Jabatan,
        //         convert(int,TotalRemun)as TotalRemun,convert(int,Kelebihan)as Kelebihan,convert(int,Kekurangan) as Kekurangan,convert(int,SimpananWajib) as SimpananWajib,
        // convert(int,Koperasi)as Koperasi,convert(int,SimpananPokok)as SimpananPokok, convert(int,BAMC)as BAMC,convert(int,Obat) as Obat,convert(int,UangDuka) as UangDuka,
        // convert(int,Infaq)as Infaq,convert(int,Rokris)as Rokris,convert(int,Tombokan)as Tombokan,convert(int,Futsal) as Futsal,convert(int,PPH21) as PPH21,convert(int,Lainlain)as Lainlain,
        // convert(int,TotalNett) as TotalNett from V_Remon where idpegawai = '$idpegawai'  and Tahun = '$pilihtahun'
        return $this->db_ketiga->query($query)->row();
    }
    public function dataoncall()
    {
        $this->db_kedua = $this->load->database('db2', true);
        $idpegawai = $this->session->userdata('idpegawai');
        $bln =  $this->input->get('bln');
        $thn =  $this->input->get('thn');
        $query = "SELECT *,0 as gaji,0 as jagut,0 as jasadokter,0 as nerus,case when nilaiuangEdited is null then NilaiUang else nilaiuangEdited 
        end as oncall,0 as remunerasi,0 as shift from V_UangOnCallREMUNERASI where month(tglbayar)='$bln' AND year(tglbayar)= '$thn' and idpegawai='$idpegawai'  ";
        // var_dump($query);
        // die;
        return $this->db_kedua->query($query)->row();
    }
    public function datashift()
    {
        $this->db_kedua = $this->load->database('db2', true);
        $idpegawai = $this->session->userdata('idpegawai');
        $pilihtanggal = $this->input->post('pilihtanggal');
        $tanggal = date('Y-m-d', strtotime($pilihtanggal));
        $bln =  $this->input->get('bln');
        $thn =  $this->input->get('thn');

        $query = "SELECT u.NamaLengkap,TglBayar ,u.tanggal TglKegiatan,kdwaktu + ' ' + ket as Ket,u.NilaiUang,u.nilaiUangEdit AS NilaiPenyesuaian,
        CASE WHEN nilaiUangEdit IS NOT NULL THEN nilaiUangEdit ELSE nilaiuang END NilaiAkhir, u.AlasanEdit AlasanPenyesuaian,'Shift' Judul   
        from V_UangShiftREMUNERASI u WHERE MONTH(u.tglbayar)= '$bln' and year(u.tglbayar)= '$thn' and statusenabled = '1'
         AND CASE WHEN nilaiUangEdit IS NOT NULL THEN nilaiUangEdit ELSE nilaiuang END> 0 and idpegawai = '$idpegawai'
        ";
        return $this->db_kedua->query($query)->result_array();
        // var_dump($query);
        // die;
    }
    public function datanerus()
    {
        $this->db_kedua = $this->load->database('db2', true);
        $idpegawai = $this->session->userdata('idpegawai');
        $pilihtanggal = $this->input->post('pilihtanggal');
        $tanggal = date('Y-m-d', strtotime($pilihtanggal));
        $bln =  $this->input->get('bln');
        $thn =  $this->input->get('thn');

        $query = "SELECT u.NamaLengkap,TglBayar ,u.tanggal TglKegiatan,kdwaktu + ' ' + ket as Ket,u.NilaiUang,u.nilaiUangEdited AS NilaiPenyesuaian,CASE WHEN nilaiUangEdited IS NOT NULL THEN nilaiUangEdited ELSE nilaiuang END NilaiAkhir, u.AlasanEdit AlasanPenyesuaian,'Nerus' Judul
        from V_UangShiftNerusREMUNERASI u WHERE MONTH(u.tglbayar)= '$bln' and year(u.tglbayar)= '$thn'  and statusenabled = '1' 
     AND CASE WHEN nilaiUangEdited IS NOT NULL THEN nilaiUangEdited ELSE nilaiuang END> 0 and idpegawai = '$idpegawai'
        ";
        return $this->db_kedua->query($query)->result_array();
        // var_dump($query);
        // die;
    }
    public function datajagut()
    {
        $this->db_kedua = $this->load->database('db2', true);
        $idpegawai = $this->session->userdata('idpegawai');
        $pilihtanggal = $this->input->post('pilihtanggal');
        $tanggal = date('Y-m-d', strtotime($pilihtanggal));
        $bln =  $this->input->get('bln');
        $thn =  $this->input->get('thn');

        $query = "SELECT u.NamaLengkap,TglBayar ,u.tanggal TglKegiatan,kdwaktu + ' ' + ket as Ket,u.NilaiUang,u.nilaiUangEdited AS NilaiPenyesuaian,CASE WHEN nilaiUangEdited IS NOT NULL THEN nilaiUangEdited ELSE nilaiuang END NilaiAkhir, u.AlasanEdit AlasanPenyesuaian,'Jagut' Judul
        from V_UangShiftJagutREMUNERASI u WHERE MONTH(u.tglbayar)= '$bln' and year(u.tglbayar)= '$thn'  and statusenabled = '1' 
    AND CASE WHEN nilaiUangEdited IS NOT NULL THEN nilaiUangEdited ELSE nilaiuang END> 0 and idpegawai = '$idpegawai'";
        return $this->db_kedua->query($query)->result_array();
        // var_dump($query);
        // die;
    }
    public function subkomite()
    {
        $this->db_ketiga = $this->load->database('db3', true);
        $idpegawai = $this->session->userdata('idpegawai');
        $pilihtahun = $this->input->post('pilihtahun');
        $bln =  $this->input->get('bln');
        $thn =  $this->input->get('thn');
        $query = "	SELECT  *,0 as gaji,0 as jagut,0 as jasadokter,0 as nerus,0 as oncall,0 
        as remunerasi,0 as shift,'15'+'/'+cast(bln as varchar(5))+'/'+cast(thn as varchar(5)) as Tanggal  
        from V_ListTunjanganRadiasi_Posting WHERE   year(tglbayar) = '$thn' AND   month(tglbayar) = '$bln' and idpegawai='$idpegawai'";
        //         SELECT NamaPegawai,Bulan,Tahun,Jabatan,
        //         convert(int,TotalRemun)as TotalRemun,convert(int,Kelebihan)as Kelebihan,convert(int,Kekurangan) as Kekurangan,convert(int,SimpananWajib) as SimpananWajib,
        // convert(int,Koperasi)as Koperasi,convert(int,SimpananPokok)as SimpananPokok, convert(int,BAMC)as BAMC,convert(int,Obat) as Obat,convert(int,UangDuka) as UangDuka,
        // convert(int,Infaq)as Infaq,convert(int,Rokris)as Rokris,convert(int,Tombokan)as Tombokan,convert(int,Futsal) as Futsal,convert(int,PPH21) as PPH21,convert(int,Lainlain)as Lainlain,
        // convert(int,TotalNett) as TotalNett from V_Remon where idpegawai = '$idpegawai'  and Tahun = '$pilihtahun'

        return $this->db_ketiga->query($query)->row();
    }
    public function datauangshift()
    {
        $this->db_kedua = $this->load->database('db2', true);
        $idpegawai = $this->session->userdata('idpegawai');
        $pilihtanggal = $this->input->post('pilihtanggal');
        $tanggal = date('Y-m-d', strtotime($pilihtanggal));
        $bulan = date('m', strtotime($pilihtanggal));
        $tahun = date('Y', strtotime($pilihtanggal));
        $query = "SELECT u.NamaLengkap,TglBayar ,u.tanggal TglKegiatan,kdwaktu + ' ' + ket as Ket,u.NilaiUang,u.nilaiUangEdit AS NilaiPenyesuaian,CASE WHEN nilaiUangEdit IS NOT NULL THEN nilaiUangEdit ELSE nilaiuang END NilaiAkhir, u.AlasanEdit AlasanPenyesuaian,'Shift' Judul   from V_UangShiftREMUNERASI u 
        WHERE MONTH(u.tglbayar)= '$bulan' and year(u.tglbayar)= '2020' and statusenabled = '1' AND CASE WHEN nilaiUangEdit IS NOT NULL THEN nilaiUangEdit ELSE nilaiuang END> 0 and idpegawai = '$idpegawai'
         UNION ALL  SELECT u.NamaLengkap,TglBayar ,u.tanggal TglKegiatan,kdwaktu + ' ' + ket as Ket,u.NilaiUang,u.nilaiUangEdited AS NilaiPenyesuaian,CASE WHEN nilaiUangEdited IS NOT NULL THEN nilaiUangEdited ELSE nilaiuang END NilaiAkhir, u.AlasanEdit AlasanPenyesuaian,'Nerus' Judul
          from V_UangShiftNerusREMUNERASI u WHERE MONTH(u.tglbayar)= '$bulan' and year(u.tglbayar)= '$tahun'  and statusenabled = '1' 
       AND CASE WHEN nilaiUangEdited IS NOT NULL THEN nilaiUangEdited ELSE nilaiuang END> 0 and idpegawai = '$idpegawai'
          UNION ALL
         SELECT u.NamaLengkap,TglBayar ,u.tanggal TglKegiatan,'OnCall' Ket,u.NilaiUang,u.nilaiUangEdited AS NilaiPenyesuaian,CASE WHEN nilaiUangEdited IS NOT NULL THEN nilaiUangEdited ELSE nilaiuang END NilaiAkhir, u.AlasanEdit AlasanPenyesuaian,'On Call' Judul
           from V_UangOnCallREMUNERASI u WHERE  MONTH(u.tglbayar)= '$bulan' and year(u.tglbayar)= '$tahun'    AND CASE WHEN nilaiUangEdited IS NOT NULL THEN nilaiUangEdited ELSE nilaiuang END> 0     and idpegawai = '$idpegawai'
             UNION ALL
         SELECT u.NamaLengkap,TglBayar ,u.tanggal TglKegiatan,kdwaktu + ' ' + ket as Ket,u.NilaiUang,u.nilaiUangEdited AS NilaiPenyesuaian,CASE WHEN nilaiUangEdited IS NOT NULL THEN nilaiUangEdited ELSE nilaiuang END NilaiAkhir, u.AlasanEdit AlasanPenyesuaian,'Jagut' Judul
           from V_UangShiftJagutREMUNERASI u WHERE MONTH(u.tglbayar)= '$bulan' and year(u.tglbayar)= '$tahun'  and statusenabled = '1' 
       AND CASE WHEN nilaiUangEdited IS NOT NULL THEN nilaiUangEdited ELSE nilaiuang END> 0 and idpegawai = '$idpegawai'";
        return $this->db_kedua->query($query)->result_array();
        // var_dump($query);
        // die;
    }
    public function datagaji()
    {
        $this->db_kedua = $this->load->database('db2', true);
        $idpegawai = $this->session->userdata('idpegawai');
        $pilihtanggal = $this->input->post('pilihtanggal');
        $tanggal = date('Y-m-d', strtotime($pilihtanggal));
        $query = "exec v_gaji_Pergub95_tampil '$tanggal', '$idpegawai','3'";
        return $this->db_kedua->query($query)->result_array();
    }


    public function datadiri()
    {
        $this->db_kedua = $this->load->database('db2', true);
        $idpegawai = $this->session->userdata('idpegawai');
        $query = "SELECT *, NamaLengkap, convert(varchar,TglLahir,101)as tgllahir2
        , convert(varchar,TglTHLAwal,101)as TglTHLAwal2
        , convert(varchar,TglTHLAkhir,101)as TglTHLAkhir2
        , convert(varchar,TglCobaAwal,101)as TglCobaAwal2
        , convert(varchar,TglCobaAkhir,101)as TglCobaAkhir2
        , convert(varchar,TglKontrakIAwal,101)as TglKontrakIAwal2
        , convert(varchar,TglKontrakIAkhir,101)as TglKontrakIAkhir2
        , convert(varchar,TglKontrakIIAwal,101)as TglKontrakIIAwal2
        , convert(varchar,TglKontrakIIAkhir,101)as TglKontrakIIAkhir2
        , convert(varchar,TglKontrakKrywnAwal,101)as TglKontrakKrywnAwal2
        , convert(varchar,TglKontrakKrywnAkhir,101)as TglKontrakKrywnAkhir2
        , convert(varchar,TglCpnsAwal,101)as TglCpnsAwal2
        , convert(varchar,TglCPNSAkhir,101)as TglCPNSAkhir2
        , convert(varchar,TglTMT,101)as TglTMT2
        , convert(varchar,TglPensiun,101)as TglPensiun2
        , convert(varchar,tglPenyesuaian,101)as tglPenyesuaian2
    from V_M_DataPegawaiLaporan where IDp = '$idpegawai'";
        // var_dump($query);
        // die;
        return $this->db_kedua->query($query)->row();
    }

    public function datalogabsen()
    {
        $this->db_kedua = $this->load->database('db2', true);
        $idpegawai = $this->session->userdata('idpegawai');
        $pilihtahun = $this->input->post('bulanabsen');
        $month = date("m", strtotime($pilihtahun));
        $year = date("Y", strtotime($pilihtahun));
        $query = "SELECT *,CONVERT(varchar,TglLog,106)as tgllog2, CONVERT(varchar,TglLog,108)as jamlog from V_AbsenLog 
                where (IdPegawai = '$idpegawai') and (MONTH(TglLog) = $month) AND (YEAR(TglLog) = $year)order by TglLog asc";
        // var_dump($query);
        // die;
        return $this->db_kedua->query($query)->result_array();
        // var_dump($pilihtahun);
        // var_dump($month);
        // var_dump($year);
        // die;
    }
}
