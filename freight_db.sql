-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 12, 2026 at 03:31 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `freight_diary`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_coupon`
--

CREATE TABLE `academic_coupon` (
  `CouponID` int(8) NOT NULL,
  `CouponNo` varchar(20) NOT NULL,
  `BgDate` date NOT NULL,
  `Date` date NOT NULL,
  `NTDate` date NOT NULL,
  `Title` varchar(100) NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `active_accounts`
--

CREATE TABLE `active_accounts` (
  `IE_Main` int(11) NOT NULL,
  `CashReceipt` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `active_account_receivable`
--

CREATE TABLE `active_account_receivable` (
  `AccountNo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_account_receivable_view`
-- (See below for the actual view)
--
CREATE TABLE `active_account_receivable_view` (
`AccountNo` int(11)
,`AccountName` varchar(150)
);

-- --------------------------------------------------------

--
-- Table structure for table `active_bank_cash`
--

CREATE TABLE `active_bank_cash` (
  `AccountID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_bank_cash_view`
-- (See below for the actual view)
--
CREATE TABLE `active_bank_cash_view` (
`AccountID` int(11)
,`AccountName` varchar(150)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_consignment_commodities`
-- (See below for the actual view)
--
CREATE TABLE `active_consignment_commodities` (
`ConsignmentID` int(11)
,`BL` varchar(50)
,`ConsigneeID` int(11)
,`ETA` date
,`CmdtTypeID` int(11)
,`ReleaseType` int(11)
,`Destination` text
,`BranchID` varchar(10)
,`Date` date
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_consignment_commodities_1`
-- (See below for the actual view)
--
CREATE TABLE `active_consignment_commodities_1` (
`ConsignmentID` int(11)
,`BL` varchar(50)
,`ConsigneeID` int(11)
,`ETA` date
,`CmdtTypeID` int(11)
,`CommodityType` text
,`ReleaseType` int(11)
,`Destination` text
,`BranchID` varchar(10)
,`Date` date
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_consignment_commodities_2`
-- (See below for the actual view)
--
CREATE TABLE `active_consignment_commodities_2` (
`ConsignmentID` int(11)
,`BL` varchar(50)
,`ConsigneeID` int(11)
,`ETA` date
,`CmdtTypeID` int(11)
,`CommodityType` text
,`ReleaseTypeID` int(11)
,`ReleaseType` text
,`Destination` text
,`BranchID` varchar(10)
,`Date` date
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `active_consignment_revenue`
--

CREATE TABLE `active_consignment_revenue` (
  `AccountNo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `active_declaration_income`
--

CREATE TABLE `active_declaration_income` (
  `AccountNo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `active_handling_cost`
--

CREATE TABLE `active_handling_cost` (
  `AccountNo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `active_ie`
--

CREATE TABLE `active_ie` (
  `AccountID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_ie_view`
-- (See below for the actual view)
--
CREATE TABLE `active_ie_view` (
`AccountID` int(11)
,`AccountName` varchar(150)
);

-- --------------------------------------------------------

--
-- Table structure for table `active_momo`
--

CREATE TABLE `active_momo` (
  `AccountNo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_momo_view`
-- (See below for the actual view)
--
CREATE TABLE `active_momo_view` (
`AccountNo` int(11)
,`AccountName` varchar(150)
);

-- --------------------------------------------------------

--
-- Table structure for table `active_petty`
--

CREATE TABLE `active_petty` (
  `AccountNo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `active_petty_cash`
--

CREATE TABLE `active_petty_cash` (
  `AccountNo` int(11) NOT NULL,
  `Username` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_petty_cash_view`
-- (See below for the actual view)
--
CREATE TABLE `active_petty_cash_view` (
`AccountNo` int(11)
,`AccountName` varchar(150)
,`Dr` double(19,2)
,`Cr` double(19,2)
,`Bal` double(19,2)
,`Username` varchar(10)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_petty_cash_view_0`
-- (See below for the actual view)
--
CREATE TABLE `active_petty_cash_view_0` (
`AccountNo` int(11)
,`AccountName` varchar(150)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`TBal` double(19,2)
,`Username` varchar(10)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_petty_view`
-- (See below for the actual view)
--
CREATE TABLE `active_petty_view` (
`AccountNo` int(11)
,`AccountName` varchar(150)
);

-- --------------------------------------------------------

--
-- Table structure for table `active_service_charge`
--

CREATE TABLE `active_service_charge` (
  `AccountNo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_students`
-- (See below for the actual view)
--
CREATE TABLE `active_students` (
`StudentID` varchar(30)
,`FullName` varchar(161)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`CurrentClassID` int(11)
,`CurrentClassName` varchar(150)
,`STelNo` varchar(15)
,`TelNo` varchar(15)
,`Date` date
,`Time` date
,`Username` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_students_fee`
-- (See below for the actual view)
--
CREATE TABLE `active_students_fee` (
`StudentID` varchar(30)
,`FullName` varchar(161)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`CurrentClassID` int(11)
,`CurrentClassName` varchar(150)
,`STelNo` varchar(15)
,`TelNo` varchar(15)
,`Balance` double(19,2)
,`Date` date
,`Time` date
,`Username` varchar(20)
,`Status` int(11)
,`BranchID` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_student_ticket_term_rpt`
-- (See below for the actual view)
--
CREATE TABLE `active_student_ticket_term_rpt` (
`TicketNo` varchar(15)
,`SubClassID` varchar(10)
,`StudentID` varchar(40)
,`CouponNo` varchar(100)
,`Validation` int(11)
,`Date` date
,`Time` datetime
,`Username` varchar(15)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `active_vault`
--

CREATE TABLE `active_vault` (
  `AccountNo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `active_write_off`
--

CREATE TABLE `active_write_off` (
  `AccountNo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_write_off_view`
-- (See below for the actual view)
--
CREATE TABLE `active_write_off_view` (
`AccountNo` int(11)
,`AccountName` varchar(150)
,`Type` varchar(15)
);

-- --------------------------------------------------------

--
-- Table structure for table `algor`
--

CREATE TABLE `algor` (
  `TicketNo` varchar(15) NOT NULL,
  `SubClassID` varchar(10) NOT NULL,
  `StudentID` varchar(40) NOT NULL,
  `CouponNo` varchar(100) NOT NULL,
  `Validation` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `algor_view`
-- (See below for the actual view)
--
CREATE TABLE `algor_view` (
`TicketNo` varchar(15)
,`SubClassID` varchar(10)
,`SubClassName` varchar(150)
,`StudentID` varchar(40)
,`FullName` varchar(161)
,`CouponNo` varchar(100)
,`Validation` int(11)
,`Date` date
,`Time` datetime
,`Username` varchar(15)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `all_date`
--

CREATE TABLE `all_date` (
  `TDate` date NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_details`
--

CREATE TABLE `bank_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `BankName` varchar(100) NOT NULL,
  `AccountName` varchar(150) NOT NULL,
  `AccountNo` varchar(50) NOT NULL,
  `Branch` varchar(100) DEFAULT NULL,
  `MomoQR` varchar(255) DEFAULT NULL,
  `MerchantID` varchar(50) DEFAULT NULL,
  `MerchantName` varchar(100) DEFAULT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT 1,
  `Username` varchar(15) NOT NULL DEFAULT 'system',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `billing_board`
--

CREATE TABLE `billing_board` (
  `CouponID` varchar(100) NOT NULL,
  `Date` date NOT NULL,
  `ReceiptNo` varchar(30) NOT NULL,
  `SubClassID` int(11) NOT NULL,
  `Description` longtext NOT NULL,
  `Note` longtext NOT NULL,
  `PmtStartDate` date NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `bsheet_view`
-- (See below for the actual view)
--
CREATE TABLE `bsheet_view` (
`AccountID` int(11)
,`SubAccountID` int(11)
,`Mode` varchar(5)
,`TType` varchar(5)
,`ReceiptNo` varchar(30)
,`Dr` float
,`Cr` float
,`Description` longtext
,`Date` date
,`FDate` varchar(30)
,`Time` timestamp
,`Username` varchar(11)
,`BranchID` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `bsheet_view_1`
-- (See below for the actual view)
--
CREATE TABLE `bsheet_view_1` (
`AccountID` int(11)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`Date` date
,`FDate` varchar(30)
,`BranchID` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `bsheet_view_2`
-- (See below for the actual view)
--
CREATE TABLE `bsheet_view_2` (
`ControlID` int(11)
,`ControlName` varchar(100)
,`CategoryID` int(11)
,`Class` varchar(2)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`Diff` double(19,2)
,`Date` date
,`FDate` varchar(30)
,`BranchID` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `bsheet_view_3`
-- (See below for the actual view)
--
CREATE TABLE `bsheet_view_3` (
`ControlID` int(11)
,`ControlName` varchar(100)
,`CategoryID` int(11)
,`CategoryName` varchar(100)
,`SubCategoryID` int(11)
,`SubCategoryName` varchar(100)
,`Class` varchar(2)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`Diff` double(19,2)
,`Date` date
,`FDate` varchar(30)
,`BranchID` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `cargo_manifestation_breakdown`
-- (See below for the actual view)
--
CREATE TABLE `cargo_manifestation_breakdown` (
`ConsignmentID` int(11)
,`MainBL` varchar(30)
,`HouseBL` varchar(50)
,`ConsigneeID` int(11)
,`Consigenee2_ID` int(11)
,`Description` longtext
,`Weight` float
,`Package` int(11)
,`Unit` varchar(7)
,`Username` varchar(15)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `case-group`
--

CREATE TABLE `case-group` (
  `GroupID` int(11) NOT NULL,
  `GroupName` varchar(120) NOT NULL,
  `MaxMember` int(11) NOT NULL,
  `CreatedBy` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ccdb`
--

CREATE TABLE `ccdb` (
  `ID` int(11) NOT NULL,
  `Message` longtext NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `charge_taxes`
--

CREATE TABLE `charge_taxes` (
  `GetFund` float NOT NULL,
  `Covid` double NOT NULL,
  `NHIL` double NOT NULL,
  `VAT` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_category`
--

CREATE TABLE `class_category` (
  `CategoryID` int(11) NOT NULL,
  `CategoryName` varchar(100) NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_main`
--

CREATE TABLE `class_main` (
  `CategoryID` int(11) NOT NULL,
  `ClassID` int(11) NOT NULL,
  `ClassName` varchar(100) NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `class_main_view`
-- (See below for the actual view)
--
CREATE TABLE `class_main_view` (
`CategoryID` int(11)
,`ClassID` int(11)
,`ClassName` varchar(100)
,`Username` varchar(15)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `class_population`
-- (See below for the actual view)
--
CREATE TABLE `class_population` (
`Poopulation` bigint(21)
,`CurrentClassID` int(11)
,`CouponNo` varchar(150)
);

-- --------------------------------------------------------

--
-- Table structure for table `class_subject`
--

CREATE TABLE `class_subject` (
  `ClassID` int(11) NOT NULL,
  `SubjCategoryID` int(11) NOT NULL,
  `SubjectID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `class_subject_view`
-- (See below for the actual view)
--
CREATE TABLE `class_subject_view` (
`CourseID` int(11)
,`CourseName` varchar(100)
,`SubjCategoryID` int(11)
,`SubjCategoryName` varchar(100)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`Date` date
,`Time` datetime
,`Username` varchar(15)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `commodity_category`
--

CREATE TABLE `commodity_category` (
  `ID` int(11) NOT NULL,
  `CategoryName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `commodity_type`
--

CREATE TABLE `commodity_type` (
  `CategoryID` int(11) NOT NULL,
  `TypeID` int(11) NOT NULL,
  `TypeName` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consignee_main`
--

CREATE TABLE `consignee_main` (
  `ConsigneeID` int(11) NOT NULL,
  `FullName` longtext NOT NULL,
  `TelNo` varchar(30) NOT NULL,
  `Address1` longtext NOT NULL,
  `Address2` longtext NOT NULL,
  `Address3` longtext NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `consignment_profile_view`
-- (See below for the actual view)
--
CREATE TABLE `consignment_profile_view` (
`ConsignmentID` int(11)
,`ETA` date
,`BL` varchar(50)
,`ContainerNo` varchar(30)
,`ContainerSize` varchar(15)
,`ShipperID` int(11)
,`ContWeight` float
,`Destination` text
,`BranchID` varchar(10)
,`Date` date
,`Time` datetime
,`Status` int(11)
,`CommodityCategoryID` int(11)
,`CmdtTypeID` int(11)
,`CommodityType` text
,`ConsigneeID` int(11)
,`ConsigneeName` longtext
,`ReleaseTypeID` int(11)
,`ReleaseType` text
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `consignment_profile_view_1`
-- (See below for the actual view)
--
CREATE TABLE `consignment_profile_view_1` (
`ConsignmentID` int(11)
,`ETA` date
,`BL` varchar(50)
,`ContainerNo` varchar(30)
,`ContainerSize` varchar(15)
,`ShipperID` int(11)
,`ShipperName` longtext
,`ContWeight` float
,`Destination` text
,`BranchID` varchar(10)
,`Date` date
,`Time` datetime
,`Status` int(11)
,`CommodityCategoryID` int(11)
,`CommodityCategory` varchar(50)
,`CmdtTypeID` int(11)
,`CommodityType` text
,`ConsigneeID` int(11)
,`ConsigneeName` longtext
,`ReleaseTypeID` int(11)
,`ReleaseType` text
);

-- --------------------------------------------------------

--
-- Table structure for table `consignment_weight_temp`
--

CREATE TABLE `consignment_weight_temp` (
  `MainBL` varchar(30) NOT NULL,
  `HBL` varchar(20) NOT NULL,
  `ConsignmentID` varchar(10) NOT NULL,
  `Weight` float NOT NULL,
  `Username` varchar(10) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `consignment_weight_temp_view`
-- (See below for the actual view)
--
CREATE TABLE `consignment_weight_temp_view` (
`MainBL` varchar(30)
,`ConsignmentID` varchar(10)
,`Total` double(19,2)
,`Username` varchar(10)
);

-- --------------------------------------------------------

--
-- Table structure for table `container_cmdts_temp`
--

CREATE TABLE `container_cmdts_temp` (
  `MarksNumbers` text NOT NULL,
  `ContainerSize` float NOT NULL,
  `ItemDetails` int(11) NOT NULL,
  `Username` varchar(10) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `container_details`
--

CREATE TABLE `container_details` (
  `ConsignmentID` int(11) NOT NULL,
  `BL` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `SealNo` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `ContainerNo` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `ContainerSize` varchar(15) NOT NULL,
  `Weight` float NOT NULL,
  `ItemDetails` text NOT NULL,
  `HandlingCost` float NOT NULL,
  `GateOutDate` date DEFAULT NULL,
  `ReturnDate` date DEFAULT NULL,
  `Username` varchar(15) NOT NULL,
  `BranchID` varchar(10) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `container_exp_pmt`
-- (See below for the actual view)
--
CREATE TABLE `container_exp_pmt` (
`ConsignmentID` int(11)
,`MainBL` varchar(30)
,`HouseBL` varchar(20)
,`ConsigneeID` int(11)
,`ReceiptNo` varchar(30)
,`AccountNo` int(11)
,`Fee` float
,`GetFundNHIL` float
,`VAT` float
,`Date` date
,`Time` datetime
,`Username` varchar(15)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `container_exp_pmt_0`
-- (See below for the actual view)
--
CREATE TABLE `container_exp_pmt_0` (
`ConsignmentID` int(11)
,`MainBL` varchar(30)
,`ConsCount` bigint(21)
,`TFee` double(19,2)
,`GetFundNHIL` float
,`VAT` float
,`Date` date
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `container_exp_pmt_1`
-- (See below for the actual view)
--
CREATE TABLE `container_exp_pmt_1` (
`ConsignmentID` int(11)
,`ShipperName` varchar(150)
,`VesselName` varchar(80)
,`SealNo` varchar(50)
,`ETA` date
,`MainBL` varchar(30)
,`ConsCount` bigint(21)
,`TFee` double(19,2)
,`TDr` double(19,2)
,`GetFundNHIL` float
,`GTFVal` double(19,2)
,`VAT` float
,`VATVal` double(19,2)
,`Date` date
,`TDate` date
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `container_exp_pmt_2`
-- (See below for the actual view)
--
CREATE TABLE `container_exp_pmt_2` (
`ConsignmentID` int(11)
,`ShipperName` varchar(150)
,`VesselName` varchar(80)
,`SealNo` varchar(50)
,`ETA` date
,`MainBL` varchar(30)
,`ConsCount` bigint(21)
,`TFee` double(19,2)
,`TDr` double(19,2)
,`Balance` double(19,2)
,`GetFundNHIL` float
,`GTFVal` double(19,2)
,`VAT` float
,`VATVal` double(19,2)
,`TotalTax` double(19,2)
,`Date` date
,`TDate` date
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `container_exp_pmt_3`
-- (See below for the actual view)
--
CREATE TABLE `container_exp_pmt_3` (
`ConsignmentID` int(11)
,`ShipperName` varchar(150)
,`VesselName` varchar(80)
,`SealNo` varchar(50)
,`ETA` date
,`MainBL` varchar(30)
,`ConsCount` bigint(21)
,`TFee` double(19,2)
,`TDr` double(19,2)
,`Balance` double(19,2)
,`GetFundNHIL` float
,`GTFVal` double(19,2)
,`VAT` float
,`VATVal` double(19,2)
,`TotalTax` double(19,2)
,`Date` date
,`TDate` date
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `container_exp_pmt_view`
-- (See below for the actual view)
--
CREATE TABLE `container_exp_pmt_view` (
`AccountID` int(11)
,`Stamp` varchar(3)
,`Mode` varchar(10)
,`MainBL` varchar(150)
,`HouseBL` varchar(150)
,`ReceiptNo` varchar(30)
,`Description` longtext
,`Dr` float
,`Cr` float
,`Date` date
,`Time` timestamp
,`BranchID` int(11)
,`Username` varchar(11)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `container_exp_pmt_view_0`
-- (See below for the actual view)
--
CREATE TABLE `container_exp_pmt_view_0` (
`AccountID` int(11)
,`Stamp` varchar(3)
,`Mode` varchar(10)
,`MainBL` varchar(150)
,`HouseBL` varchar(150)
,`ReceiptNo` varchar(30)
,`Description` longtext
,`TDr` double(19,2)
,`Cr` float
,`Date` date
,`Time` timestamp
,`BranchID` int(11)
,`Username` varchar(11)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `container_gate_out_view`
-- (See below for the actual view)
--
CREATE TABLE `container_gate_out_view` (
`ConsignmentID` int(11)
,`ConsigneeID` int(11)
,`BL` varchar(50)
,`SealNo` varchar(50)
,`ContainerNo` varchar(50)
,`ContainerSize` varchar(15)
,`Weight` float
,`HandlingCost` float
,`Status` int(11)
,`GateOutDate` date
,`ReturnedDate` date
,`TodayDate` date
,`Demurrage` int(7)
,`Username` varchar(15)
,`BranchID` varchar(10)
,`Date` date
,`Time` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `container_main`
--

CREATE TABLE `container_main` (
  `ConsignmentID` int(11) NOT NULL,
  `CarrierID` int(11) NOT NULL,
  `Rotation` varchar(30) NOT NULL,
  `ShipperID` int(11) NOT NULL,
  `VesselName` varchar(80) NOT NULL,
  `VoyageNo` varchar(80) NOT NULL,
  `SealNo` varchar(50) NOT NULL,
  `ETA` date NOT NULL,
  `BL` varchar(50) NOT NULL,
  `ContainerNo` varchar(30) NOT NULL,
  `ContainerSize` varchar(15) NOT NULL,
  `ReceiptNo` varchar(30) NOT NULL,
  `POIS` varchar(80) NOT NULL,
  `DOIS` date NOT NULL,
  `SOB` date NOT NULL,
  `POL_ID` int(11) NOT NULL,
  `POD_ID` int(11) NOT NULL,
  `ContWeight` float NOT NULL,
  `Charges` float NOT NULL,
  `AgentContact` varchar(20) NOT NULL,
  `Destination` text NOT NULL,
  `Username` varchar(15) NOT NULL,
  `BranchID` varchar(10) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Status` int(11) NOT NULL,
  `CmdtTypeID` int(11) NOT NULL,
  `ConsigneeID` int(11) NOT NULL,
  `ReleaseType` int(11) NOT NULL,
  `Ownership` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `container_main_view`
-- (See below for the actual view)
--
CREATE TABLE `container_main_view` (
`ConsignmentID` int(11)
,`ShipperID` int(11)
,`ShipperName` varchar(150)
,`VesselName` varchar(80)
,`VoyageNo` varchar(80)
,`SealNo` varchar(50)
,`ETA` date
,`BL` varchar(50)
,`ContainerNo` varchar(50)
,`SOB` date
,`ContainerSize` varchar(15)
,`POL_ID` int(11)
,`POL_Name` varchar(60)
,`POD_ID` int(11)
,`POD_Name` varchar(60)
,`ContWeight` double(19,2)
,`AgentContact` varchar(20)
,`Username` varchar(15)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `container_main_view_0`
-- (See below for the actual view)
--
CREATE TABLE `container_main_view_0` (
`ConsignmentID` int(11)
,`ShipperID` int(11)
,`ShipperName` varchar(150)
,`VesselName` varchar(80)
,`ETA` date
,`BL` varchar(50)
,`ContainerNo` varchar(50)
,`ContainerSize` varchar(15)
,`POL_ID` int(11)
,`POL_Name` varchar(60)
,`POD_ID` int(11)
,`POD_Name` varchar(60)
,`ContWeight` double(19,2)
,`TWeight` double(19,2)
,`Username` varchar(15)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `container_main_view_1`
-- (See below for the actual view)
--
CREATE TABLE `container_main_view_1` (
`ConsignmentID` int(11)
,`ShipperID` int(11)
,`ShipperName` varchar(150)
,`VesselName` varchar(80)
,`ETA` date
,`ETA_Days` int(7)
,`BL` varchar(50)
,`ContainerNo` varchar(50)
,`ContainerSize` varchar(15)
,`POL_ID` int(11)
,`POL_Name` varchar(60)
,`POD_ID` int(11)
,`POD_Name` varchar(60)
,`ContWeight` double(19,2)
,`TWeight` double(19,2)
,`Username` varchar(15)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `container_main_view_2`
-- (See below for the actual view)
--
CREATE TABLE `container_main_view_2` (
`ConsignmentID` int(11)
,`ShipperID` int(11)
,`ShipperName` varchar(150)
,`VesselName` varchar(80)
,`ETA` date
,`BL` varchar(50)
,`ContainerNo` varchar(50)
,`ContainerSize` varchar(15)
,`POL_ID` int(11)
,`POL_Name` varchar(60)
,`POD_ID` int(11)
,`POD_Name` varchar(60)
,`ContWeight` double(19,2)
,`TempWeight` double(19,2)
,`TWeight` double(19,2)
,`Username` varchar(15)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `container_main_view_3`
-- (See below for the actual view)
--
CREATE TABLE `container_main_view_3` (
`ConsignmentID` int(11)
,`ShipperID` int(11)
,`ShipperName` varchar(150)
,`VesselName` varchar(80)
,`ETA` date
,`BL` varchar(50)
,`ContainerNo` varchar(50)
,`ContainerSize` varchar(15)
,`POL_ID` int(11)
,`POL_Name` varchar(60)
,`POD_ID` int(11)
,`POD_Name` varchar(60)
,`ContWeight` double(19,2)
,`TempWeight` double(19,2)
,`TWeight` double(19,2)
,`Username` varchar(15)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `container_main_view_total_weight`
-- (See below for the actual view)
--
CREATE TABLE `container_main_view_total_weight` (
`ConsignmentID` int(11)
,`BL` varchar(50)
,`BLWeight` double(19,2)
,`TWeight` double(19,2)
,`Username` varchar(15)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `container_release`
--

CREATE TABLE `container_release` (
  `ID` int(11) NOT NULL,
  `ReleaseType` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `correction_container_size_view`
-- (See below for the actual view)
--
CREATE TABLE `correction_container_size_view` (
`Rotation` varchar(30)
,`ShipperID` int(11)
,`VesselName` varchar(80)
,`VoyageNo` varchar(80)
,`SealNo` varchar(50)
,`SealNo1` varchar(50)
,`ETA` date
,`BL` varchar(50)
,`ContainerNo` varchar(30)
,`ContainerNo1` varchar(50)
,`ContainerSize` varchar(15)
,`ContainerSize1` varchar(15)
,`ReceiptNo` varchar(30)
,`ContWeight` float
,`Charges` float
,`HandlingCost` float
,`AgentContact` varchar(20)
);

-- --------------------------------------------------------

--
-- Table structure for table `ctrl_fee_receivable`
--

CREATE TABLE `ctrl_fee_receivable` (
  `AccountID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `ctrl_fee_receivable_view`
-- (See below for the actual view)
--
CREATE TABLE `ctrl_fee_receivable_view` (
`AccountID` int(11)
,`AccountName` varchar(150)
);

-- --------------------------------------------------------

--
-- Table structure for table `ctrl_student`
--

CREATE TABLE `ctrl_student` (
  `AccountID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `ctrl_student_view`
-- (See below for the actual view)
--
CREATE TABLE `ctrl_student_view` (
`AccountID` int(11)
,`AccountName` varchar(150)
);

-- --------------------------------------------------------

--
-- Table structure for table `currency_conversion`
--

CREATE TABLE `currency_conversion` (
  `Rate` float NOT NULL,
  `Currency` varchar(5) NOT NULL,
  `Username` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `declaration_main`
--

CREATE TABLE `declaration_main` (
  `DeclarationID` int(11) NOT NULL,
  `BL` varchar(50) NOT NULL,
  `DeclarationNo` varchar(50) NOT NULL,
  `ItemDescription` longtext NOT NULL,
  `DutyPaid` float NOT NULL,
  `Amount` int(11) NOT NULL,
  `AgentName` longtext NOT NULL,
  `AgentContact` varchar(30) NOT NULL,
  `ContainerSize` varchar(50) NOT NULL,
  `ReceiptNo` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(15) NOT NULL,
  `BranchID` varchar(10) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `declaration_main_view`
-- (See below for the actual view)
--
CREATE TABLE `declaration_main_view` (
`DeclarationID` int(11)
,`BL` varchar(50)
,`DeclarationNo` varchar(50)
,`ItemDescription` longtext
,`DutyPaid` float
,`Amount` int(11)
,`AgentName` longtext
,`AgentContact` varchar(30)
,`ContainerSize` varchar(50)
,`ReceiptNo` varchar(30)
,`Date` date
,`Time` datetime
,`Username` varchar(15)
,`FullName` varchar(180)
,`BranchID` varchar(10)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `declaration_main_view_0`
-- (See below for the actual view)
--
CREATE TABLE `declaration_main_view_0` (
`DeclarationID` int(11)
,`MainBL` varchar(30)
,`HBL` varchar(50)
,`ConsigneeID` int(11)
,`ConsigneeName` longtext
,`DeclarationNo` varchar(50)
,`ItemDescription` longtext
,`DutyPaid` float
,`Amount` int(11)
,`AgentName` longtext
,`AgentContact` varchar(30)
,`ContainerSize` varchar(50)
,`ReceiptNo` varchar(30)
,`Date` date
,`Time` datetime
,`Username` varchar(15)
,`FullName` varchar(180)
,`BranchID` varchar(10)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `declaration_process_search`
-- (See below for the actual view)
--
CREATE TABLE `declaration_process_search` (
`ConsignmentID` int(11)
,`MainBL` varchar(30)
,`HouseBL` varchar(50)
,`ContainerSize` varchar(15)
,`Weight` float
,`Description` longtext
,`OtherInfo` longtext
,`AgentContact` varchar(20)
);

-- --------------------------------------------------------

--
-- Table structure for table `disburement_income_account`
--

CREATE TABLE `disburement_income_account` (
  `AccountNo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disburement_user_auth`
--

CREATE TABLE `disburement_user_auth` (
  `Authorisor` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disbursement_accounts`
--

CREATE TABLE `disbursement_accounts` (
  `AccountNo` int(11) NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `disbursement_accounts_view`
-- (See below for the actual view)
--
CREATE TABLE `disbursement_accounts_view` (
`AccountNo` int(11)
,`AccountName` varchar(150)
,`Username` varchar(15)
,`Date` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `disbursement_analysis`
--

CREATE TABLE `disbursement_analysis` (
  `ConsigneeID` varchar(15) NOT NULL,
  `BL` varchar(30) NOT NULL,
  `HBL` varchar(10) NOT NULL,
  `ContainerNo` varchar(30) NOT NULL,
  `TotalCashReceipt` float NOT NULL,
  `ReceiptNo` varchar(20) NOT NULL,
  `AccountID` int(11) NOT NULL,
  `Revenue` float NOT NULL,
  `Expenditure` float NOT NULL,
  `Stamp` varchar(20) NOT NULL,
  `Username` varchar(10) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Status` int(11) NOT NULL,
  `Type` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `disbursement_analysis_chart`
-- (See below for the actual view)
--
CREATE TABLE `disbursement_analysis_chart` (
`ConsigneeID` varchar(15)
,`BL` varchar(30)
,`ETA` date
,`HBL` varchar(10)
,`ContainerNo` varchar(30)
,`TotalCashReceipt` float
,`ReceiptNo` varchar(20)
,`AccountID` int(11)
,`Revenue` float
,`Expenditure` float
,`Username` varchar(10)
,`Date` date
,`Time` datetime
,`Status` int(11)
,`Type` varchar(15)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `disbursement_analysis_chart_0`
-- (See below for the actual view)
--
CREATE TABLE `disbursement_analysis_chart_0` (
`ConsigneeID` varchar(15)
,`BL` varchar(30)
,`HBL` varchar(10)
,`ContainerNo` varchar(30)
,`TotalCashReceipt` float
,`ReceiptNo` varchar(20)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`Revenue` float
,`Expenditure` float
,`Username` varchar(10)
,`Date` date
,`Time` datetime
,`Status` int(11)
,`Type` varchar(15)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `disbursement_analysis_distinct_hbl`
-- (See below for the actual view)
--
CREATE TABLE `disbursement_analysis_distinct_hbl` (
`ContainerNo` varchar(30)
,`BL` varchar(30)
,`HBL` varchar(10)
,`TotalCashReceipt` float
,`ReceiptNo` varchar(20)
,`TotalExpenditure` double(19,2)
,`Date` date
,`Status` int(11)
,`Type` varchar(15)
,`Time` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `disbursement_analysis_distinct_hbl_0`
-- (See below for the actual view)
--
CREATE TABLE `disbursement_analysis_distinct_hbl_0` (
`ContainerNo` varchar(30)
,`TotalCashReceipt` float
,`BL_COUNT` bigint(21)
,`TExpenditure` double(19,2)
,`Date` date
,`Status` int(11)
,`Type` varchar(15)
,`Time` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `disbursement_analysis_unauth`
-- (See below for the actual view)
--
CREATE TABLE `disbursement_analysis_unauth` (
`ConsigneeID` varchar(15)
,`BL` varchar(30)
,`HBL` varchar(10)
,`ContainerNo` varchar(30)
,`TotalCashReceipt` float
,`ReceiptNo` varchar(20)
,`AccountID` int(11)
,`Expenditure` float
,`Username` varchar(10)
,`Date` date
,`Time` datetime
,`Status` int(11)
,`Type` varchar(15)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `disbursement_analysis_unauth_0`
-- (See below for the actual view)
--
CREATE TABLE `disbursement_analysis_unauth_0` (
`HBL` varchar(30)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `disbursement_analysis_unauth_1`
-- (See below for the actual view)
--
CREATE TABLE `disbursement_analysis_unauth_1` (
`ConsigneeID` varchar(15)
,`BL` varchar(30)
,`HBL` varchar(10)
,`ContainerNo` varchar(30)
,`TotalCashReceipt` float
,`ReceiptNo` varchar(20)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`Expenditure` float
,`Username` varchar(10)
,`Date` date
,`Time` datetime
,`Status` int(11)
,`Type` varchar(15)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `disbursement_analysis_unauth_2`
-- (See below for the actual view)
--
CREATE TABLE `disbursement_analysis_unauth_2` (
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `disbursement_analysis_unauth_3`
-- (See below for the actual view)
--
CREATE TABLE `disbursement_analysis_unauth_3` (
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `disbursement_analysis_unauth_4`
-- (See below for the actual view)
--
CREATE TABLE `disbursement_analysis_unauth_4` (
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `disbursement_analysis_view`
-- (See below for the actual view)
--
CREATE TABLE `disbursement_analysis_view` (
`ConsigneeID` varchar(15)
,`BL` varchar(30)
,`HBL` varchar(10)
,`ContainerNo` varchar(30)
,`TotalCashReceipt` float
,`ReceiptNo` varchar(20)
,`AccountID` int(11)
,`Expenditure` float
,`Username` varchar(10)
,`Date` date
,`Time` datetime
,`Status` int(11)
,`Type` varchar(15)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `disbursement_analysis_view_0`
-- (See below for the actual view)
--
CREATE TABLE `disbursement_analysis_view_0` (
`Destination` text
,`BL` varchar(30)
,`CarrierID` int(11)
,`HBL` varchar(10)
,`ContainerNo` varchar(30)
,`TotalCashReceipt` float
,`ReceiptNo` varchar(20)
,`AccountID` int(11)
,`Expenditure` float
,`Username` varchar(10)
,`Date` date
,`Time` datetime
,`Status` int(11)
,`Type` varchar(15)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `disbursement_analysis_view_1`
-- (See below for the actual view)
--
CREATE TABLE `disbursement_analysis_view_1` (
`Destination` text
,`BL` varchar(30)
,`CarrierID` int(11)
,`HBL` varchar(10)
,`ContainerNo` varchar(30)
,`TotalCashReceipt` float
,`ReceiptNo` varchar(20)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`Expenditure` float
,`Username` varchar(10)
,`Date` date
,`Time` datetime
,`Status` int(11)
,`Type` varchar(15)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `disbursement_analysis_view_2`
-- (See below for the actual view)
--
CREATE TABLE `disbursement_analysis_view_2` (
`Destination` text
,`BL` varchar(30)
,`CarrierID` int(11)
,`Carrier` longtext
,`TotalCashReceipt` float
,`ReceiptNo` varchar(20)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`Expenditure` float
,`Username` varchar(10)
,`Date` date
,`Time` datetime
,`Status` int(11)
,`Type` varchar(15)
);

-- --------------------------------------------------------

--
-- Table structure for table `disbursement_temp_analysis`
--

CREATE TABLE `disbursement_temp_analysis` (
  `AccountNo` int(11) NOT NULL,
  `BL` varchar(20) NOT NULL,
  `HouseBL` varchar(30) NOT NULL,
  `ContainerNo` varchar(20) NOT NULL,
  `ConsigneeID` varchar(20) NOT NULL,
  `Amount` float NOT NULL,
  `Type` varchar(20) NOT NULL,
  `Status` varchar(15) NOT NULL,
  `Username` varchar(10) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `disbursement_temp_analysis_view`
-- (See below for the actual view)
--
CREATE TABLE `disbursement_temp_analysis_view` (
`AccountNo` int(11)
,`AccountName` varchar(150)
,`BL` varchar(20)
,`HouseBL` varchar(30)
,`ConsigneeID` varchar(20)
,`Amount` float
,`Type` varchar(20)
,`Status` varchar(15)
,`Username` varchar(10)
,`Time` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `disbursement_temp_analysis_view_0`
-- (See below for the actual view)
--
CREATE TABLE `disbursement_temp_analysis_view_0` (
`AccountNo` int(11)
,`AccountName` varchar(150)
,`BL` varchar(20)
,`HouseBL` varchar(30)
,`ConsigneeID` varchar(20)
,`ConsigneeName` longtext
,`Amount` float
,`Type` varchar(20)
,`Status` varchar(15)
,`Username` varchar(10)
,`Time` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `disbursment_gateout_truck_details`
--

CREATE TABLE `disbursment_gateout_truck_details` (
  `ID` int(11) NOT NULL,
  `ConsignmentID` int(11) NOT NULL,
  `BL` varchar(200) NOT NULL,
  `ReceiptNo` varchar(30) NOT NULL,
  `TruckNumber` longtext NOT NULL,
  `DriverContact` longtext NOT NULL,
  `Username` varchar(20) NOT NULL,
  `TIme` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `eta_web_track`
--

CREATE TABLE `eta_web_track` (
  `ConsignmentID` varchar(20) NOT NULL,
  `MainBL` varchar(30) NOT NULL,
  `ETA` date NOT NULL,
  `ArrivalStatus` text NOT NULL,
  `Status` text NOT NULL,
  `Username` varchar(10) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `eta_web_track_view`
-- (See below for the actual view)
--
CREATE TABLE `eta_web_track_view` (
`ConsignmentID` varchar(20)
,`MainBL` varchar(30)
,`ETA` date
,`ArrivalStatus` varchar(19)
,`Status` text
,`Username` varchar(10)
,`Time` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `eta_web_track_view_0`
-- (See below for the actual view)
--
CREATE TABLE `eta_web_track_view_0` (
`ConsignmentID` varchar(20)
,`MainBL` varchar(30)
,`HouseBL` varchar(50)
,`ETA` date
,`SealNo` varchar(50)
,`ContainerNo` varchar(50)
,`SOB` date
,`POL_Name` varchar(60)
,`POD_Name` varchar(60)
,`ArrivalStatus` varchar(19)
,`Status` text
,`Username` varchar(10)
,`Time` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `e_delivery_order_request`
--

CREATE TABLE `e_delivery_order_request` (
  `HouseBL` varchar(20) NOT NULL,
  `ReleaseType` varchar(15) NOT NULL,
  `Agency` varchar(100) NOT NULL,
  `DocumentType` varchar(15) NOT NULL,
  `UnstuffingType` varchar(15) NOT NULL,
  `Status` int(11) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `e_delivery_order_request_view`
-- (See below for the actual view)
--
CREATE TABLE `e_delivery_order_request_view` (
`HouseBL` varchar(20)
,`ReleaseType` varchar(15)
,`Agency` varchar(100)
,`DocumentType` varchar(15)
,`UnstuffingType` varchar(15)
,`Status` int(11)
,`Time` datetime
,`Type` varchar(22)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `e_online_request`
-- (See below for the actual view)
--
CREATE TABLE `e_online_request` (
`HouseBL` varchar(20)
,`Status` int(11)
,`Type` varchar(22)
);

-- --------------------------------------------------------

--
-- Table structure for table `e_payment_confirmation`
--

CREATE TABLE `e_payment_confirmation` (
  `HouseBL` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `PaymentMode` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `PaymentDetails` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `ImgUrl` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `ContactDetails` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `Time` datetime NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `e_payment_confirmation_view`
-- (See below for the actual view)
--
CREATE TABLE `e_payment_confirmation_view` (
`HouseBL` varchar(25)
,`PaymentMode` varchar(15)
,`PaymentDetails` text
,`ImgUrl` text
,`ContactDetails` text
,`Time` datetime
,`Status` int(11)
,`Type` varchar(16)
);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fee_account_order`
--

CREATE TABLE `fee_account_order` (
  `AccountID` int(11) NOT NULL,
  `Order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `fee_account_order_view`
-- (See below for the actual view)
--
CREATE TABLE `fee_account_order_view` (
`AccountID` int(11)
,`AccountName` varchar(150)
,`Order` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `financial_statement_view`
-- (See below for the actual view)
--
CREATE TABLE `financial_statement_view` (
`AccountID` int(11)
,`SubAccountID` int(11)
,`Mode` varchar(5)
,`TType` varchar(5)
,`ReceiptNo` varchar(30)
,`Dr` float
,`Cr` float
,`Description` longtext
,`Date` date
,`Time` timestamp
,`Username` varchar(11)
,`Authorizer` varchar(15)
,`BranchID` int(11)
,`Status` int(11)
,`LDate` varchar(100)
,`RptUser` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `financial_statement_view_0`
-- (See below for the actual view)
--
CREATE TABLE `financial_statement_view_0` (
`AccountID` int(11)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`TBal` double(19,2)
,`LDate` varchar(100)
,`RptUser` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `financial_statement_view_1`
-- (See below for the actual view)
--
CREATE TABLE `financial_statement_view_1` (
`ControlID` int(11)
,`ControlName` varchar(100)
,`CategoryID` int(11)
,`CategoryName` varchar(100)
,`SubCategoryID` int(11)
,`SubCategoryName` varchar(100)
,`Class` varchar(2)
,`Nature` varchar(3)
,`Type` varchar(15)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`TBal` double(19,2)
,`LDate` varchar(100)
,`RptUser` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `gateout_pending_consignment`
-- (See below for the actual view)
--
CREATE TABLE `gateout_pending_consignment` (
`ConsignmentID` int(11)
,`BL` varchar(50)
,`Destination` text
,`SealNo` varchar(50)
,`ContainerNo` varchar(50)
,`ContainerSize` varchar(15)
,`Weight` float
,`HandlingCost` float
,`Username` varchar(15)
,`BranchID` varchar(10)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `general_accounts`
-- (See below for the actual view)
--
CREATE TABLE `general_accounts` (
`AccountNo` int(11)
,`AccountName` varchar(150)
,`AccountType` varchar(15)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `general_ledger_balances`
-- (See below for the actual view)
--
CREATE TABLE `general_ledger_balances` (
`AccountID` int(11)
,`SubAccountID` int(11)
,`Mode` varchar(5)
,`TType` varchar(5)
,`Dr` double(19,2)
,`Cr` double(19,2)
,`LDate` date
,`BranchID` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `general_ledger_balances_0`
-- (See below for the actual view)
--
CREATE TABLE `general_ledger_balances_0` (
`ControlID` int(11)
,`ControlName` varchar(100)
,`CategoryID` int(11)
,`Class` varchar(2)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`SubAccountID` int(11)
,`Mode` varchar(5)
,`TType` varchar(5)
,`Dr` double(19,2)
,`Cr` double(19,2)
,`Balance` double(19,2)
,`LDate` date
,`BranchID` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `gl_statement`
-- (See below for the actual view)
--
CREATE TABLE `gl_statement` (
`AccountID` int(11)
,`AccountName` varchar(150)
,`SubAccountID` int(11)
,`Mode` varchar(5)
,`TType` varchar(5)
,`ReceiptNo` varchar(30)
,`Dr` double(19,2)
,`Cr` double(19,2)
,`Description` longtext
,`Date` date
,`Time` timestamp
,`Username` varchar(11)
,`Authorizer` varchar(15)
,`BranchID` int(11)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `gl_statement_1`
-- (See below for the actual view)
--
CREATE TABLE `gl_statement_1` (
`AccountID` int(11)
,`AccountName` varchar(150)
,`SubAccountID` int(11)
,`Mode` varchar(5)
,`TType` varchar(5)
,`ReceiptNo` varchar(30)
,`Dr` double(19,2)
,`Cr` double(19,2)
,`Description` longtext
,`Date` date
,`Time` timestamp
,`Username` varchar(11)
,`Authorizer` varchar(15)
,`BranchID` int(11)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `gl_statement_sub_account`
-- (See below for the actual view)
--
CREATE TABLE `gl_statement_sub_account` (
`AccountID` int(11)
,`AccountName` varchar(150)
,`SubAccountID` int(11)
,`SubAccountName` varchar(150)
,`Type` varchar(15)
,`Mode` varchar(5)
,`TType` varchar(5)
,`ReceiptNo` varchar(30)
,`Dr` double(19,2)
,`Cr` double(19,2)
,`Description` longtext
,`Date` date
,`Time` timestamp
,`Username` varchar(11)
,`Authorizer` varchar(15)
,`BranchID` int(11)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `graph_test`
-- (See below for the actual view)
--
CREATE TABLE `graph_test` (
`ConsignmentID` int(11)
,`MainBL` varchar(30)
,`HouseBL` varchar(50)
,`ConsigneeID` int(11)
,`FullName` longtext
,`TFee` double(19,2)
,`Consigenee2_ID` int(11)
,`Description` longtext
,`Weight` float
,`Package` int(11)
,`Unit` varchar(7)
,`Username` varchar(15)
,`TDate` date
,`Date` date
,`DMonth` int(2)
,`MonthName` varchar(3)
,`YearName` int(4)
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `graph_test_0`
-- (See below for the actual view)
--
CREATE TABLE `graph_test_0` (
`ConsignmentID` int(11)
,`MainBL` varchar(30)
,`HouseBL` varchar(50)
,`ConsigneeID` int(11)
,`FullName` longtext
,`TFee` double(19,2)
,`Weight` float
,`Unit` varchar(7)
,`Username` varchar(15)
,`TDate` date
,`Date` date
,`DMonth` int(2)
,`MonthName` varchar(3)
,`YearName` int(4)
,`MonthYear` varchar(15)
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `graph_test_1`
-- (See below for the actual view)
--
CREATE TABLE `graph_test_1` (
`DMonth` int(2)
,`MonthName` varchar(3)
,`YearName` int(4)
,`MonthYear` varchar(15)
,`TWeight` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `GroupID` int(11) NOT NULL,
  `GroupName` varchar(120) NOT NULL,
  `MaxMember` int(11) NOT NULL,
  `CreatedBy` varchar(20) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_members`
--

CREATE TABLE `group_members` (
  `GroupID` int(11) NOT NULL,
  `MemberID` varchar(30) NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `group_members_view`
-- (See below for the actual view)
--
CREATE TABLE `group_members_view` (
`GroupID` int(11)
,`GroupName` varchar(120)
,`MemberID` varchar(30)
,`MemberName` varchar(100)
,`Username` varchar(15)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `handling_charge`
--

CREATE TABLE `handling_charge` (
  `AccountNo` int(11) NOT NULL,
  `Amount` float NOT NULL,
  `POrder` int(11) NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `handling_charge_view`
-- (See below for the actual view)
--
CREATE TABLE `handling_charge_view` (
`AccountNo` int(11)
,`AccountName` varchar(150)
,`Amount` float
,`PaymentOrder` int(11)
,`Username` varchar(15)
,`Time` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `hbl_invoice`
--

CREATE TABLE `hbl_invoice` (
  `ConsignmentID` int(11) NOT NULL,
  `MainBL` varchar(30) NOT NULL,
  `HouseBL` varchar(20) NOT NULL,
  `ConsigneeID` int(11) NOT NULL,
  `ReceiptNo` varchar(30) NOT NULL,
  `AccountNo` int(11) NOT NULL,
  `Fee` float NOT NULL,
  `GetFundNHIL` float NOT NULL,
  `VAT` float NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hbl_invoice_consignee_temp`
--

CREATE TABLE `hbl_invoice_consignee_temp` (
  `ConsignmentID` int(11) NOT NULL,
  `MainBL` varchar(30) NOT NULL,
  `HouseBL` varchar(30) NOT NULL,
  `ConsigneeID` int(11) NOT NULL,
  `AccountNo` int(11) NOT NULL,
  `GetFundNHIL` float NOT NULL,
  `Covid` float NOT NULL,
  `VAT` float NOT NULL,
  `Amount` float NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `hbl_invoice_consignee_temp_view`
-- (See below for the actual view)
--
CREATE TABLE `hbl_invoice_consignee_temp_view` (
`ConsignmentID` int(11)
,`MainBL` varchar(30)
,`HouseBL` varchar(30)
,`ConsigneeID` int(11)
,`FullName` longtext
,`Description` longtext
,`Weight` float
,`Package` int(11)
,`Unit` varchar(7)
,`AccountNo` int(11)
,`AccountName` varchar(150)
,`Amount` double(19,2)
,`GetFund` double(19,2)
,`SubTotal` double(19,2)
,`VAT` float
,`Date` date
,`Time` datetime
,`Username` varchar(15)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `hbl_invoice_consignee_temp_view_0`
-- (See below for the actual view)
--
CREATE TABLE `hbl_invoice_consignee_temp_view_0` (
`ConsignmentID` int(11)
,`MainBL` varchar(30)
,`HouseBL` varchar(30)
,`ConsigneeID` int(11)
,`FullName` longtext
,`Description` longtext
,`Weight` float
,`Package` int(11)
,`Unit` varchar(7)
,`AccountNo` int(11)
,`AccountName` varchar(150)
,`Amount` double(19,2)
,`GetFund` double(19,2)
,`SubTotal` double(19,2)
,`VAT` double(19,2)
,`SubTotalTax` double(19,2)
,`GetFundPcnt` float
,`VATPcnt` float
,`Date` date
,`Time` datetime
,`Username` varchar(15)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `hbl_invoice_view`
-- (See below for the actual view)
--
CREATE TABLE `hbl_invoice_view` (
`ConsignmentID` varchar(11)
,`MainBL` varchar(50)
,`HouseBL` varchar(50)
,`ConsigneeID` varchar(30)
,`Description` longtext
,`ReceiptNo` varchar(30)
,`AccountNo` int(11)
,`Fee` float
,`GetFundNHIL` float
,`VAT` float
,`Stamp` varchar(5)
,`Date` date
,`Time` datetime
,`Username` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `hbl_invoice_view_0`
-- (See below for the actual view)
--
CREATE TABLE `hbl_invoice_view_0` (
`ConsignmentID` varchar(11)
,`MainBL` varchar(50)
,`HouseBL` varchar(50)
,`ConsigneeID` varchar(30)
,`ReceiptNo` varchar(30)
,`AccountNo` int(11)
,`TFee` double(19,2)
,`GetFundNHIL` float
,`VAT` float
,`Date` date
,`Time` datetime
,`Username` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `hbl_invoice_view_0_0`
-- (See below for the actual view)
--
CREATE TABLE `hbl_invoice_view_0_0` (
`ConsignmentID` varchar(11)
,`MainBL` varchar(50)
,`HouseBL` varchar(50)
,`ConsigneeID` varchar(30)
,`ReceiptNo` varchar(30)
,`AccountNo` int(11)
,`AccountName` varchar(150)
,`ItemDescription` longtext
,`Description` longtext
,`Fee` float
,`GetFundNHIL` float
,`GetVal` double(19,2)
,`SubTotal` double(19,2)
,`VAT` float
,`Stamp` varchar(5)
,`Date` date
,`Time` datetime
,`Username` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `hbl_invoice_view_0_1`
-- (See below for the actual view)
--
CREATE TABLE `hbl_invoice_view_0_1` (
`ConsignmentID` varchar(11)
,`MainBL` varchar(50)
,`HouseBL` varchar(50)
,`ConsigneeID` varchar(30)
,`FullName` longtext
,`ReceiptNo` varchar(30)
,`AccountNo` int(11)
,`AccountName` varchar(150)
,`ItemDescription` longtext
,`Description` longtext
,`Fee` float
,`GetFundNHIL` float
,`GetVal` double(19,2)
,`SubTotal` double(19,2)
,`VAT` float
,`VATVal` double(19,2)
,`TotalCharges` double(19,2)
,`Stamp` varchar(5)
,`Date` date
,`Time` datetime
,`Username` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `hbl_invoice_view_1`
-- (See below for the actual view)
--
CREATE TABLE `hbl_invoice_view_1` (
`ConsignmentID` varchar(11)
,`ShipperID` int(11)
,`MainBL` varchar(50)
,`HouseBL` varchar(50)
,`ConsigneeID` varchar(30)
,`FullName` longtext
,`Consignee2_ID` int(11)
,`ReceiptNo` varchar(30)
,`AccountNo` int(11)
,`TFee` double(19,2)
,`GetFundNHIL` float
,`VAT` float
,`GTFVal` double(19,2)
,`SubTotal` double(19,2)
,`VATVal` double(19,2)
,`Date` date
,`Time` datetime
,`Username` varchar(20)
,`UserFullName` varchar(180)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `house`
--

CREATE TABLE `house` (
  `HouseID` int(11) NOT NULL,
  `House` varchar(50) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `ie_transaction_journal`
-- (See below for the actual view)
--
CREATE TABLE `ie_transaction_journal` (
`AccountID` int(11)
,`SubAccountID` int(11)
,`MainIE` int(11)
,`Mode` varchar(5)
,`TType` varchar(5)
,`ReceiptNo` varchar(30)
,`Dr` float
,`Cr` float
,`Description` longtext
,`Date` date
,`Time` timestamp
,`Username` varchar(11)
,`BranchID` int(11)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `ie_transaction_journal_0`
-- (See below for the actual view)
--
CREATE TABLE `ie_transaction_journal_0` (
`ControlID` int(11)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`CategoryID` int(11)
,`CategoryName` varchar(100)
,`SubCategoryID` int(11)
,`SubCategoryName` varchar(100)
,`MainIE` int(11)
,`Type` varchar(15)
,`Mode` varchar(5)
,`TType` varchar(5)
,`ReceiptNo` varchar(30)
,`Dr` float
,`Cr` float
,`Description` longtext
,`Date` date
,`Time` timestamp
,`Username` varchar(11)
,`BranchID` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `ie_transaction_journal_1`
-- (See below for the actual view)
--
CREATE TABLE `ie_transaction_journal_1` (
`ControlID` int(11)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`CategoryID` int(11)
,`CategoryName` varchar(100)
,`SubCategoryID` int(11)
,`SubCategoryName` varchar(100)
,`MainIE` int(11)
,`Type` varchar(15)
,`Mode` varchar(5)
,`TType` varchar(5)
,`ReceiptNo` varchar(30)
,`Dr` float
,`Cr` float
,`Description` longtext
,`Date` date
,`Time` timestamp
,`Username` varchar(11)
,`BranchID` int(11)
,`FDate` varchar(100)
,`LDate` varchar(100)
,`RptUser` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `ie_transaction_journal_2`
-- (See below for the actual view)
--
CREATE TABLE `ie_transaction_journal_2` (
`ControlID` int(11)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`Type` varchar(15)
,`CategoryID` int(11)
,`CategoryName` varchar(100)
,`SubCategoryID` int(11)
,`SubCategoryName` varchar(100)
,`MainIE` int(11)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`TBal` double(19,2)
,`BranchID` int(11)
,`FDate` varchar(100)
,`LDate` varchar(100)
,`RptUser` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `ie_transaction_journal_balance`
-- (See below for the actual view)
--
CREATE TABLE `ie_transaction_journal_balance` (
`AccountID` int(11)
,`SubAccountID` int(11)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`BranchID` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `ie_transaction_journal_balance_0`
-- (See below for the actual view)
--
CREATE TABLE `ie_transaction_journal_balance_0` (
`AccountID` int(11)
,`SubAccountID` int(11)
,`AccountName` varchar(150)
,`Type` varchar(15)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`TBal` double(19,2)
,`BranchID` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `ie_transaction_journal_general`
-- (See below for the actual view)
--
CREATE TABLE `ie_transaction_journal_general` (
`ControlID` int(11)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`CategoryID` int(11)
,`CategoryName` varchar(100)
,`SubCategoryID` int(11)
,`SubCategoryName` varchar(100)
,`MainIE` int(11)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`TBal` double(19,2)
,`BranchID` int(11)
,`FDate` varchar(100)
,`LDate` varchar(100)
,`RptUser` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `inharbor_pending`
-- (See below for the actual view)
--
CREATE TABLE `inharbor_pending` (
`ConsignmentID` int(11)
,`CarrierID` int(11)
,`Rotation` varchar(30)
,`ShipperID` int(11)
,`VesselName` varchar(80)
,`VoyageNo` varchar(80)
,`SealNo` varchar(50)
,`ETA` date
,`BL` varchar(50)
,`ConsigneeID` int(11)
,`ConsigneeName` longtext
,`ContainerNo` varchar(30)
,`ContainerSize` varchar(15)
,`ReceiptNo` varchar(30)
,`POIS` varchar(80)
,`DOIS` date
,`SOB` date
,`POL_ID` int(11)
,`POD_ID` int(11)
,`ContWeight` float
,`Charges` float
,`AgentContact` varchar(20)
,`Destination` text
,`Username` varchar(15)
,`BranchID` varchar(10)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `inharbor_pending_1`
-- (See below for the actual view)
--
CREATE TABLE `inharbor_pending_1` (
`ConsignmentID` int(11)
,`CarrierID` int(11)
,`ConsigneeID` int(11)
,`ConsigneeName` longtext
,`Rotation` varchar(30)
,`ShipperID` int(11)
,`VesselName` varchar(80)
,`VoyageNo` varchar(80)
,`SealNo` varchar(50)
,`ETA` date
,`ETADays` int(7)
,`BL` varchar(50)
,`ContainerNo` varchar(30)
,`ContainerSize` varchar(15)
,`ContWeight` float
,`Charges` float
,`AgentContact` varchar(20)
,`Destination` text
,`Username` varchar(15)
,`BranchID` varchar(10)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `inst_branch`
--

CREATE TABLE `inst_branch` (
  `InstID` int(11) NOT NULL,
  `BranchID` int(11) NOT NULL,
  `Branch` varchar(100) NOT NULL,
  `BranchName` varchar(150) NOT NULL,
  `Address` varchar(150) NOT NULL,
  `TelNo` varchar(50) NOT NULL,
  `Location` varchar(100) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `inst_branch_view`
-- (See below for the actual view)
--
CREATE TABLE `inst_branch_view` (
`InstID` int(11)
,`InstName` varchar(150)
,`Website` text
,`Email` varchar(150)
,`BranchID` int(11)
,`BranchName` varchar(150)
,`Address` varchar(150)
,`TelNo` varchar(50)
,`Location` varchar(100)
,`Date` date
,`Time` datetime
,`Username` varchar(20)
);

-- --------------------------------------------------------

--
-- Table structure for table `inst_reg`
--

CREATE TABLE `inst_reg` (
  `InstID` int(11) NOT NULL,
  `InstName` varchar(150) NOT NULL,
  `Initial` varchar(5) NOT NULL,
  `Website` text NOT NULL,
  `Email` varchar(150) NOT NULL,
  `TelNo` varchar(40) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal`
--

CREATE TABLE `journal` (
  `AccountID` int(11) NOT NULL,
  `SubAccountID` int(11) NOT NULL,
  `Mode` varchar(5) NOT NULL,
  `TType` varchar(5) NOT NULL,
  `ReceiptNo` varchar(30) NOT NULL,
  `Dr` float NOT NULL,
  `Cr` float NOT NULL,
  `Description` longtext NOT NULL,
  `Date` date NOT NULL,
  `Time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Username` varchar(11) NOT NULL,
  `Authorizer` varchar(15) NOT NULL,
  `BranchID` int(11) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `journal_view`
-- (See below for the actual view)
--
CREATE TABLE `journal_view` (
`ControlID` int(11)
,`CategoryID` int(11)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`SubAccountID` int(11)
,`Mode` varchar(5)
,`TType` varchar(5)
,`ReceiptNo` varchar(30)
,`Dr` double(19,2)
,`Cr` double(19,2)
,`Description` longtext
,`Date` date
,`Time` timestamp
,`Username` varchar(11)
,`Authorizer` varchar(15)
,`BranchID` int(11)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `journal_view_0`
-- (See below for the actual view)
--
CREATE TABLE `journal_view_0` (
`ControlID` int(11)
,`CategoryID` int(11)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`SubAccountID` int(11)
,`SubAccountName` varchar(150)
,`Mode` varchar(5)
,`TType` varchar(5)
,`ReceiptNo` varchar(30)
,`Dr` double(19,2)
,`Cr` double(19,2)
,`Description` longtext
,`Date` date
,`Time` timestamp
,`Username` varchar(11)
,`Authorizer` varchar(15)
,`BranchID` int(11)
,`BranchName` varchar(150)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `kaina`
--

CREATE TABLE `kaina` (
  `ID` varchar(30) NOT NULL,
  `FullName` varchar(180) NOT NULL,
  `Initial` varchar(5) NOT NULL,
  `Password` varchar(20) NOT NULL,
  `HashPassword` text NOT NULL,
  `Nature` varchar(20) NOT NULL,
  `Stats` int(11) NOT NULL,
  `BranchID` int(11) NOT NULL,
  `reset_requested` tinyint(4) NOT NULL DEFAULT 0,
  `must_change_password` tinyint(4) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `kaina_view`
-- (See below for the actual view)
--
CREATE TABLE `kaina_view` (
`ID` varchar(30)
,`FullName` varchar(180)
,`Initial` varchar(5)
,`Password` varchar(20)
,`NewPass` varchar(32)
,`Nature` varchar(20)
,`Stats` int(11)
,`BranchID` int(11)
,`BranchName` varchar(100)
);

-- --------------------------------------------------------

--
-- Table structure for table `ledger_account`
--

CREATE TABLE `ledger_account` (
  `ControlID` int(11) NOT NULL,
  `CategoryID` int(11) NOT NULL,
  `Class` varchar(2) NOT NULL,
  `Nature` varchar(3) NOT NULL,
  `Type` varchar(15) NOT NULL,
  `AccountNo` int(11) NOT NULL,
  `AccountName` varchar(150) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Status` int(11) NOT NULL,
  `Visible` int(11) NOT NULL,
  `Username` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `ledger_account_expenditure`
-- (See below for the actual view)
--
CREATE TABLE `ledger_account_expenditure` (
`ControlID` int(11)
,`CategoryID` int(11)
,`SubCategoryName` varchar(100)
,`Class` varchar(2)
,`Nature` varchar(12)
,`Type` varchar(15)
,`AccountNo` int(11)
,`AccountName` varchar(150)
,`Date` date
,`Time` datetime
,`Status` int(11)
,`Visible` int(11)
,`Username` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `ledger_account_gl`
-- (See below for the actual view)
--
CREATE TABLE `ledger_account_gl` (
`ControlID` int(11)
,`CategoryID` int(11)
,`Class` varchar(2)
,`Nature` varchar(3)
,`Type` varchar(15)
,`AccountNo` int(11)
,`AccountName` varchar(150)
,`Date` date
,`Time` datetime
,`Status` int(11)
,`Visible` int(11)
,`Username` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `ledger_account_income`
-- (See below for the actual view)
--
CREATE TABLE `ledger_account_income` (
`ControlID` int(11)
,`CategoryID` int(11)
,`SubCategoryName` varchar(100)
,`Class` varchar(2)
,`Nature` varchar(12)
,`Type` varchar(15)
,`AccountNo` int(11)
,`AccountName` varchar(150)
,`Date` date
,`Time` datetime
,`Status` int(11)
,`Visible` int(11)
,`Username` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `ledger_account_view`
-- (See below for the actual view)
--
CREATE TABLE `ledger_account_view` (
`ControlID` int(11)
,`ControlName` varchar(100)
,`CategoryID` int(11)
,`CategoryName` varchar(100)
,`SubCategoryID` int(11)
,`SubCategoryName` varchar(100)
,`Class` varchar(2)
,`Nature` varchar(3)
,`Type` varchar(15)
,`AccountNo` int(11)
,`AccountName` varchar(150)
,`Date` date
,`Time` datetime
,`Status` int(11)
,`Visible` int(11)
,`Username` varchar(20)
);

-- --------------------------------------------------------

--
-- Table structure for table `ledger_category`
--

CREATE TABLE `ledger_category` (
  `CategoryID` int(11) NOT NULL,
  `CategoryName` varchar(100) NOT NULL,
  `SubCategoryID` int(11) NOT NULL,
  `SubCategoryName` varchar(100) NOT NULL,
  `Class` varchar(10) NOT NULL,
  `Type` varchar(15) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_control`
--

CREATE TABLE `ledger_control` (
  `ControlID` int(11) NOT NULL,
  `ControlName` varchar(100) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `manifestation_breakdown`
--

CREATE TABLE `manifestation_breakdown` (
  `ConsignmentID` int(11) NOT NULL,
  `MainBL` varchar(30) NOT NULL,
  `ContainerNo` varchar(50) NOT NULL,
  `HouseBL` varchar(50) NOT NULL,
  `ConsigneeID` int(11) NOT NULL,
  `Consigenee2_ID` int(11) NOT NULL,
  `Description` longtext NOT NULL,
  `ItemType` varchar(15) NOT NULL,
  `VIN` longtext NOT NULL,
  `OtherInfo` longtext NOT NULL,
  `Weight` float NOT NULL,
  `Package` int(11) NOT NULL,
  `Unit` varchar(7) NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `manifestation_breakdown_cargo`
-- (See below for the actual view)
--
CREATE TABLE `manifestation_breakdown_cargo` (
`ConsignmentID` int(11)
,`ShipperID` int(11)
,`ShipperName` varchar(150)
,`VesselName` varchar(80)
,`SealNo` varchar(50)
,`ETA` date
,`ContainerNo` varchar(50)
,`ContainerSize` varchar(15)
,`POL_ID` int(11)
,`POL_Name` varchar(60)
,`POD_ID` int(11)
,`POD_Name` varchar(60)
,`ContWeight` double(19,2)
,`MainBL` varchar(30)
,`HouseBL` varchar(50)
,`ConsigneeID` int(11)
,`Consigenee2_ID` int(11)
,`FN` longtext
,`Description` longtext
,`ItemType` varchar(15)
,`VIN` longtext
,`OtherInfo` longtext
,`Weight` float
,`Package` int(11)
,`Unit` varchar(7)
,`Username` varchar(15)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `manifestation_breakdown_cargo_view`
-- (See below for the actual view)
--
CREATE TABLE `manifestation_breakdown_cargo_view` (
`ConsignmentID` int(11)
,`ShipperID` int(11)
,`ShipperName` varchar(150)
,`VesselName` varchar(80)
,`ETA` date
,`ContainerNo` varchar(50)
,`SealNo` varchar(50)
,`ContainerSize` varchar(15)
,`POL_ID` int(11)
,`POL_Name` varchar(60)
,`POD_ID` int(11)
,`POD_Name` varchar(60)
,`ContWeight` double(19,2)
,`MainBL` varchar(30)
,`HouseBL` varchar(50)
,`ConsigneeID` int(11)
,`FullName` longtext
,`TelNo` varchar(30)
,`Address1` longtext
,`Address2` longtext
,`Address3` longtext
,`Consigenee2_ID` int(11)
,`Description` longtext
,`ItemType` varchar(15)
,`VIN` longtext
,`OtherInfo` longtext
,`Weight` float
,`Package` int(11)
,`Unit` varchar(7)
,`Username` varchar(15)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `manifestation_breakdown_hbl`
-- (See below for the actual view)
--
CREATE TABLE `manifestation_breakdown_hbl` (
`ConsignmentID` int(11)
,`ShipperID` int(11)
,`MainBL` varchar(30)
,`HouseBL` varchar(50)
,`ContainerNo` varchar(50)
,`POIS` varchar(80)
,`DOIS` date
,`SOB` date
,`ConsigneeID` int(11)
,`FullName` longtext
,`Consigenee2_ID` int(11)
,`Description` longtext
,`Weight` float
,`Package` int(11)
,`Unit` varchar(7)
,`Username` varchar(15)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `manifestation_breakdown_hbl_0`
-- (See below for the actual view)
--
CREATE TABLE `manifestation_breakdown_hbl_0` (
`ConsignmentID` int(11)
,`ShipperID` int(11)
,`MainBL` varchar(30)
,`HouseBL` varchar(50)
,`ContainerNo` varchar(50)
,`SealNo` varchar(50)
,`POIS` varchar(80)
,`DOIS` date
,`SOB` date
,`ConsigneeID` int(11)
,`FullName` longtext
,`Consigenee2_ID` int(11)
,`Description` longtext
,`Weight` float
,`Package` int(11)
,`Unit` varchar(7)
,`Username` varchar(15)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `manifestation_breakdown_search`
-- (See below for the actual view)
--
CREATE TABLE `manifestation_breakdown_search` (
`ConsignmentID` int(11)
,`MainBL` varchar(30)
,`TWeight` double(19,2)
,`Username` varchar(15)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `manifestation_breakdown_view`
-- (See below for the actual view)
--
CREATE TABLE `manifestation_breakdown_view` (
`ConsignmentID` int(11)
,`MainBL` varchar(30)
,`ContainerNo` varchar(50)
,`HouseBL` varchar(50)
,`ConsigneeID` int(11)
,`FullName` longtext
,`TFee` double(19,2)
,`Consigenee2_ID` int(11)
,`Description` longtext
,`Weight` float
,`Package` int(11)
,`Unit` varchar(7)
,`Username` varchar(15)
,`TDate` date
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `manifestation_breakdown_view_0`
-- (See below for the actual view)
--
CREATE TABLE `manifestation_breakdown_view_0` (
`ConsignmentID` int(11)
,`MainBL` varchar(30)
,`ContainerNo` varchar(50)
,`HouseBL` varchar(50)
,`ConsigneeID` int(11)
,`FullName` longtext
,`Consigenee2_ID` int(11)
,`Description` longtext
,`Weight` float
,`Package` int(11)
,`Unit` varchar(7)
,`TFee` double(19,2)
,`Username` varchar(15)
,`TDate` date
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `manifest_bl_tracking`
-- (See below for the actual view)
--
CREATE TABLE `manifest_bl_tracking` (
`MainBL` varchar(30)
,`ConsignmentID` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `manifest_bl_tracking_1`
-- (See below for the actual view)
--
CREATE TABLE `manifest_bl_tracking_1` (
`MainBL` varchar(30)
,`ConsignmentID` int(11)
,`ETA` date
);

-- --------------------------------------------------------

--
-- Table structure for table `map_admission_fee`
--

CREATE TABLE `map_admission_fee` (
  `ClassID` int(11) NOT NULL,
  `AccountID` int(11) NOT NULL,
  `Amount` float NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `map_admission_fee_view`
-- (See below for the actual view)
--
CREATE TABLE `map_admission_fee_view` (
`CourseID` int(11)
,`CourseName` varchar(100)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`Amount` double(19,2)
,`Date` date
,`Time` datetime
,`Username` varchar(20)
);

-- --------------------------------------------------------

--
-- Table structure for table `map_school_fee`
--

CREATE TABLE `map_school_fee` (
  `SubClassID` int(11) NOT NULL,
  `AccountID` int(11) NOT NULL,
  `Amount` float NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `map_school_fee_view`
-- (See below for the actual view)
--
CREATE TABLE `map_school_fee_view` (
`SubClassID` int(11)
,`SubClassName` varchar(150)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`Amount` float
,`Date` date
,`Time` datetime
,`Username` varchar(20)
);

-- --------------------------------------------------------

--
-- Table structure for table `member_temp_selection`
--

CREATE TABLE `member_temp_selection` (
  `MemberID` varchar(30) NOT NULL,
  `Status` int(1) NOT NULL,
  `Username` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `member_temp_selection_view`
-- (See below for the actual view)
--
CREATE TABLE `member_temp_selection_view` (
`MemberID` varchar(30)
,`MemberName` varchar(100)
,`ClassrmName` varchar(20)
,`Picture` longtext
,`Username` varchar(20)
,`Status` int(1)
);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `my_post`
--

CREATE TABLE `my_post` (
  `postid` varchar(50) NOT NULL,
  `content` longtext NOT NULL,
  `Username` varchar(30) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime(6) NOT NULL,
  `Status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `my_post_reply`
--

CREATE TABLE `my_post_reply` (
  `postid` varchar(30) NOT NULL,
  `MemberID` varchar(30) NOT NULL,
  `reply` longtext NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `my_post_reply_view`
-- (See below for the actual view)
--
CREATE TABLE `my_post_reply_view` (
`postid` varchar(30)
,`MemberID` varchar(30)
,`FullName` varchar(100)
,`reply` longtext
,`Date` date
,`Time` datetime
,`MinsAgo` bigint(18)
,`Ago` time
,`Now` datetime
,`Status` int(1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `my_post_reply_view_0`
-- (See below for the actual view)
--
CREATE TABLE `my_post_reply_view_0` (
`postid` varchar(30)
,`MemberID` varchar(30)
,`FullName` varchar(100)
,`reply` longtext
,`Date` date
,`Time` datetime
,`MinsAgo` varchar(23)
,`Ago` time
,`Now` datetime
,`Status` int(1)
);

-- --------------------------------------------------------

--
-- Table structure for table `my_post_viewers`
--

CREATE TABLE `my_post_viewers` (
  `postid` varchar(50) NOT NULL,
  `MemberID` varchar(30) NOT NULL,
  `Like` int(1) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `my_post_viewers_view`
-- (See below for the actual view)
--
CREATE TABLE `my_post_viewers_view` (
`postid` varchar(50)
,`MemberID` varchar(30)
,`content` longtext
,`Like` int(1)
,`Date` date
,`Time` datetime
,`Status` int(1)
,`PostStats` int(1)
,`Username` varchar(30)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `my_post_viewers_view_0`
-- (See below for the actual view)
--
CREATE TABLE `my_post_viewers_view_0` (
`postid` varchar(50)
,`MemberID` varchar(30)
,`content` longtext
,`Like` int(1)
,`Picture` longtext
,`Date` date
,`Time` datetime
,`Status` int(1)
,`Username` varchar(30)
,`FullName` varchar(100)
);

-- --------------------------------------------------------

--
-- Table structure for table `new_comtainer_cmdts_temp`
--

CREATE TABLE `new_comtainer_cmdts_temp` (
  `BL` text NOT NULL,
  `ContainerNo` varchar(20) NOT NULL,
  `SealNo` varchar(50) DEFAULT NULL,
  `Size` int(11) NOT NULL,
  `ItemDetails` text NOT NULL,
  `Username` varchar(10) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `new_container_temp`
--

CREATE TABLE `new_container_temp` (
  `BOL` varchar(50) NOT NULL,
  `SealNo` varchar(50) NOT NULL,
  `ContainerNo` varchar(50) NOT NULL,
  `ContainerSize` int(11) NOT NULL,
  `Weight` float NOT NULL,
  `HandlingCost` float NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `MakerID` varchar(20) NOT NULL,
  `Task` longtext NOT NULL,
  `Status` int(11) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `KeyType` varchar(50) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `notifications_view`
-- (See below for the actual view)
--
CREATE TABLE `notifications_view` (
`MakerID` varchar(20)
,`FullName` varchar(180)
,`Task` longtext
,`Status` int(11)
,`Username` varchar(20)
,`KeyType` varchar(50)
,`Date` date
,`Time` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `notifications_view_0`
-- (See below for the actual view)
--
CREATE TABLE `notifications_view_0` (
`MakerID` varchar(20)
,`FullName` varchar(180)
,`Task` longtext
,`Status` int(11)
,`Username` varchar(20)
,`KeyType` varchar(50)
,`UserFName` varchar(180)
,`Date` date
,`Time` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `other_invoice`
--

CREATE TABLE `other_invoice` (
  `ClientID` varchar(30) NOT NULL,
  `Description` longtext NOT NULL,
  `MainBL` varchar(50) NOT NULL,
  `HouseBL` varchar(50) NOT NULL,
  `Schedules` varchar(30) NOT NULL,
  `AccountNo` int(11) NOT NULL,
  `Stamp` varchar(5) NOT NULL,
  `ReceiptNo` varchar(30) NOT NULL,
  `Amount` float NOT NULL,
  `GetFundNHIL` float NOT NULL,
  `VAT` float NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `package_unit`
--

CREATE TABLE `package_unit` (
  `Unit` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pnl_transaction`
--

CREATE TABLE `pnl_transaction` (
  `AccountID` int(11) NOT NULL,
  `Stamp` varchar(3) NOT NULL,
  `Mode` varchar(10) NOT NULL,
  `MainBL` varchar(150) NOT NULL,
  `HouseBL` varchar(150) NOT NULL,
  `ReceiptNo` varchar(30) NOT NULL,
  `Description` longtext NOT NULL,
  `Dr` float NOT NULL,
  `Cr` float NOT NULL,
  `Date` date NOT NULL,
  `Time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `BranchID` int(11) NOT NULL,
  `Username` varchar(11) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `pnl_transaction_balances`
-- (See below for the actual view)
--
CREATE TABLE `pnl_transaction_balances` (
`AccountID` int(11)
,`Stamp` varchar(3)
,`Mode` varchar(10)
,`ReceiptNo` varchar(30)
,`Description` longtext
,`Dr` float
,`Cr` float
,`Date` date
,`Time` timestamp
,`BranchID` int(11)
,`Username` varchar(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `pnl_transaction_balances_0`
-- (See below for the actual view)
--
CREATE TABLE `pnl_transaction_balances_0` (
`AccountID` int(11)
,`AccountName` varchar(150)
,`Type` varchar(15)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`BranchID` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `pnl_transaction_balances_1`
-- (See below for the actual view)
--
CREATE TABLE `pnl_transaction_balances_1` (
`AccountID` int(11)
,`AccountName` varchar(150)
,`Type` varchar(15)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`Balance` double(19,2)
,`BranchID` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `pnl_transaction_general`
-- (See below for the actual view)
--
CREATE TABLE `pnl_transaction_general` (
`AccountID` int(11)
,`AccountName` varchar(150)
,`Stamp` varchar(3)
,`Mode` varchar(10)
,`MainBL` varchar(150)
,`HouseBL` varchar(150)
,`ReceiptNo` varchar(30)
,`Description` longtext
,`Dr` float
,`Cr` float
,`Date` date
,`Time` timestamp
,`BranchID` int(11)
,`Username` varchar(11)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `pnl_transaction_recon`
-- (See below for the actual view)
--
CREATE TABLE `pnl_transaction_recon` (
`ConsignmentID` int(11)
,`CarrierID` int(11)
,`BL` varchar(50)
,`ContainerNo` varchar(30)
,`ContainerSize` varchar(15)
,`ReceiptNo` varchar(30)
,`RecNo` varchar(30)
,`ContWeight` float
,`Charges` float
);

-- --------------------------------------------------------

--
-- Table structure for table `pod`
--

CREATE TABLE `pod` (
  `POD_ID` int(11) NOT NULL,
  `POD_Name` varchar(60) NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pol`
--

CREATE TABLE `pol` (
  `POL_ID` int(11) NOT NULL,
  `POL_Name` varchar(60) NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_ids`
--

CREATE TABLE `post_ids` (
  `ID` int(11) NOT NULL,
  `PID` varchar(100) NOT NULL,
  `Username` varchar(30) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `post_likes`
-- (See below for the actual view)
--
CREATE TABLE `post_likes` (
`postid` varchar(50)
,`MemberID` varchar(30)
,`LikeCount` bigint(21)
,`Date` date
);

-- --------------------------------------------------------

--
-- Table structure for table `receipt_main`
--

CREATE TABLE `receipt_main` (
  `ID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `ReceiptNo` varchar(30) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `receipt_momo`
-- (See below for the actual view)
--
CREATE TABLE `receipt_momo` (
`Username` varchar(20)
,`RcptNo` varchar(30)
,`ID` int(11)
,`Date` date
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `receipt_momo_0`
-- (See below for the actual view)
--
CREATE TABLE `receipt_momo_0` (
`ID` int(11)
,`max(receipt_main.ReceiptNo)` varchar(30)
,`max(receipt_main.Date)` date
,`Username` varchar(20)
);

-- --------------------------------------------------------

--
-- Table structure for table `receipt_no`
--

CREATE TABLE `receipt_no` (
  `ReceiptNo` varchar(30) NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rpt_multi_values`
--

CREATE TABLE `rpt_multi_values` (
  `FDate` varchar(30) NOT NULL,
  `LDate` varchar(100) NOT NULL,
  `Sub_ClassID` varchar(30) NOT NULL,
  `SubjectID` varchar(30) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rpt_multi_values_0`
--

CREATE TABLE `rpt_multi_values_0` (
  `FDate` varchar(100) NOT NULL,
  `LDate` varchar(100) NOT NULL,
  `Value1` varchar(100) NOT NULL,
  `Value2` varchar(100) NOT NULL,
  `Value3` varchar(100) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_charge_main`
--

CREATE TABLE `service_charge_main` (
  `ServiceID` int(11) NOT NULL,
  `BL` varchar(150) NOT NULL,
  `DeclarationID` int(11) NOT NULL,
  `DeclarationNo` varchar(50) NOT NULL,
  `ConsigneeID` varchar(50) NOT NULL,
  `ConsigneeName` longtext NOT NULL,
  `Description` longtext NOT NULL,
  `Amount` float NOT NULL,
  `ReceiptNo` varchar(30) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(15) NOT NULL,
  `BranchID` varchar(5) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `service_charge_pnl`
-- (See below for the actual view)
--
CREATE TABLE `service_charge_pnl` (
`ServiceID` int(11)
,`BL` varchar(150)
,`DeclarationID` int(11)
,`DeclarationNo` varchar(50)
,`ConsigneeID` varchar(50)
,`ConsigneeName` longtext
,`Description` longtext
,`Amount` float
,`Cr` float
,`ReceiptNo` varchar(30)
,`Date` date
,`Time` datetime
,`Username` varchar(15)
,`BranchID` varchar(5)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `setcontainerno`
-- (See below for the actual view)
--
CREATE TABLE `setcontainerno` (
`ConsignmentID` int(11)
,`MainBL` varchar(30)
,`ContainerNo` varchar(50)
,`CntNo` varchar(50)
,`HouseBL` varchar(50)
,`ConsigneeID` int(11)
,`Consigenee2_ID` int(11)
,`Description` longtext
,`ItemType` varchar(15)
,`VIN` longtext
,`OtherInfo` longtext
,`Weight` float
,`Package` int(11)
,`Unit` varchar(7)
,`Username` varchar(15)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `set_accounts`
--

CREATE TABLE `set_accounts` (
  `PNL` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `set_accounts_view`
-- (See below for the actual view)
--
CREATE TABLE `set_accounts_view` (
`PNLID` int(11)
,`PNLName` varchar(150)
);

-- --------------------------------------------------------

--
-- Table structure for table `shipper_main`
--

CREATE TABLE `shipper_main` (
  `ShipperID` int(11) NOT NULL,
  `ShipperName` varchar(150) NOT NULL,
  `AddressLine1` longtext NOT NULL,
  `AddressLine2` longtext NOT NULL,
  `AddressLine3` longtext NOT NULL,
  `AddressLine4` longtext NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ship_carrier`
--

CREATE TABLE `ship_carrier` (
  `CarrierID` int(11) NOT NULL,
  `CarrierName` longtext NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_class_subj_mapp`
--

CREATE TABLE `staff_class_subj_mapp` (
  `StaffID` varchar(15) NOT NULL,
  `SubClassID` int(11) NOT NULL,
  `SubjectID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `staff_class_subj_mapp_view`
-- (See below for the actual view)
--
CREATE TABLE `staff_class_subj_mapp_view` (
`StaffID` varchar(15)
,`StaffName` varchar(120)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`Date` date
,`Time` datetime
,`Username` varchar(15)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `staff_main`
--

CREATE TABLE `staff_main` (
  `Code` int(11) NOT NULL,
  `StaffID` varchar(15) NOT NULL,
  `FullName` varchar(120) NOT NULL,
  `DOB` date NOT NULL,
  `Gender` varchar(20) NOT NULL,
  `TelNo` varchar(20) NOT NULL,
  `Address` longtext NOT NULL,
  `Appointment_Date` date NOT NULL,
  `RegdNo` varchar(50) NOT NULL,
  `SSNIT` varchar(50) NOT NULL,
  `AcademicQlf` varchar(50) NOT NULL,
  `ProfQlf` varchar(50) NOT NULL,
  `Rank` varchar(50) NOT NULL,
  `PromoDate` date NOT NULL,
  `PostedDate` date NOT NULL,
  `SchlDate` date NOT NULL,
  `ClassTaught` varchar(50) NOT NULL,
  `Type` varchar(25) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(25) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `staff_main_view`
-- (See below for the actual view)
--
CREATE TABLE `staff_main_view` (
`Code` int(11)
,`StaffID` varchar(15)
,`FullName` varchar(120)
,`DOB` date
,`Gender` varchar(20)
,`TelNo` varchar(20)
,`Address` longtext
,`Appointment_Date` date
,`RegdNo` varchar(50)
,`SSNIT` varchar(50)
,`AcademicQlf` varchar(50)
,`ProfQlf` varchar(50)
,`Rank` varchar(50)
,`PromoDate` date
,`PostedDate` date
,`SchlDate` date
,`ClassTaught` varchar(50)
,`Type` varchar(25)
,`Date` date
,`Time` datetime
,`Username` varchar(25)
,`Status` int(11)
,`Picture` longtext
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `staff_main_view_0`
-- (See below for the actual view)
--
CREATE TABLE `staff_main_view_0` (
`Code` int(11)
,`StaffID` varchar(15)
,`FullName` varchar(120)
,`DOB` date
,`Gender` varchar(20)
,`TelNo` varchar(20)
,`Address` longtext
,`Appointment_Date` date
,`RegdNo` varchar(50)
,`SSNIT` varchar(50)
,`AcademicQlf` varchar(50)
,`ProfQlf` varchar(50)
,`Rank` varchar(50)
,`PromoDate` date
,`PostedDate` date
,`SchlDate` date
,`ClassTaught` varchar(50)
,`Type` varchar(25)
,`Date` date
,`Time` datetime
,`Username` varchar(25)
,`Status` int(11)
,`Picture` longtext
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_cont_assesment_0`
-- (See below for the actual view)
--
CREATE TABLE `student_cont_assesment_0` (
`CouponNo` varchar(150)
,`StudentID` varchar(100)
,`SubClassID` int(11)
,`TestType` varchar(10)
,`TestID` int(11)
,`Type` varchar(200)
,`TypeID` int(11)
,`TestName` varchar(200)
,`MaxScore` float
,`SubjectID` int(11)
,`Score` float
,`Username` varchar(20)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_cont_assesment_1`
-- (See below for the actual view)
--
CREATE TABLE `student_cont_assesment_1` (
`CouponNo` varchar(150)
,`StudentID` varchar(100)
,`SubClassID` int(11)
,`TypeID` int(11)
,`TypeName` varchar(200)
,`TestID` int(11)
,`TestName` varchar(200)
,`MaxScore` float
,`SubjectID` int(11)
,`Score` float
,`Username` varchar(20)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_cont_assesment_class_test`
-- (See below for the actual view)
--
CREATE TABLE `student_cont_assesment_class_test` (
`CouponNo` varchar(150)
,`StudentID` varchar(100)
,`SubClassID` int(11)
,`TypeID` int(11)
,`TypeName` varchar(200)
,`TestID` int(11)
,`TestName` varchar(200)
,`MaxScore` float
,`SubjectID` int(11)
,`Score` float
,`Username` varchar(20)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_cont_assesment_main`
-- (See below for the actual view)
--
CREATE TABLE `student_cont_assesment_main` (
`CouponNo` varchar(150)
,`Title` varchar(100)
,`StudentID` varchar(100)
,`FullName` varchar(161)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`TypeID` int(11)
,`TypeName` varchar(200)
,`TestID` int(11)
,`TestName` varchar(200)
,`MaxScore` float
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`Score` float
,`Username` varchar(20)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_current_class`
-- (See below for the actual view)
--
CREATE TABLE `student_current_class` (
`StudentID` varchar(30)
,`FullName` varchar(161)
,`PreviousClass` int(11)
,`SubCurrentClassID` int(11)
,`SubCurrentClassName` varchar(150)
,`Date` date
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `student_fee`
--

CREATE TABLE `student_fee` (
  `StudentID` int(11) NOT NULL,
  `SubClassID` varchar(30) NOT NULL,
  `CouponID` longtext NOT NULL,
  `AccountNo` int(11) NOT NULL,
  `Stamp` varchar(10) NOT NULL,
  `Description` longtext NOT NULL,
  `ReceiptNo` varchar(30) NOT NULL,
  `Dr` float NOT NULL,
  `Cr` float NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_fee_outstading_view`
-- (See below for the actual view)
--
CREATE TABLE `student_fee_outstading_view` (
`StudentID` int(11)
,`SubClassID` varchar(30)
,`CouponID` longtext
,`AccountNo` int(11)
,`Stamp` varchar(10)
,`Description` longtext
,`ReceiptNo` varchar(30)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`Date` date
,`Time` datetime
,`Username` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_fee_outstading_view_0`
-- (See below for the actual view)
--
CREATE TABLE `student_fee_outstading_view_0` (
`StudentID` int(11)
,`FullName` longtext
,`TelNo` varchar(30)
,`SubClassID` varchar(30)
,`CouponID` longtext
,`AccountNo` int(11)
,`Stamp` varchar(10)
,`Description` longtext
,`ReceiptNo` varchar(30)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`Balance` double(19,2)
,`Date` date
,`Time` datetime
,`Username` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_fee_outstading_view_1`
-- (See below for the actual view)
--
CREATE TABLE `student_fee_outstading_view_1` (
`StudentID` int(11)
,`FullName` longtext
,`SubClassID` varchar(30)
,`CouponID` longtext
,`AccountNo` int(11)
,`Stamp` varchar(10)
,`Description` longtext
,`ReceiptNo` varchar(30)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`Balance` double(19,2)
,`Date` date
,`Time` datetime
,`Username` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_fee_outstading_view_order`
-- (See below for the actual view)
--
CREATE TABLE `student_fee_outstading_view_order` (
`StudentID` int(11)
,`SubClassID` varchar(30)
,`CouponID` longtext
,`AccountNo` int(11)
,`AccountName` varchar(150)
,`PaymentOrder` int(11)
,`Stamp` varchar(10)
,`Description` longtext
,`ReceiptNo` varchar(30)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`Balance` double(19,2)
,`Date` date
,`Time` datetime
,`Username` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_fee_outstading_view_order_1`
-- (See below for the actual view)
--
CREATE TABLE `student_fee_outstading_view_order_1` (
`StudentID` int(11)
,`SubClassID` varchar(30)
,`CouponID` longtext
,`AccountNo` int(11)
,`PmtOrder` int(11)
,`Stamp` varchar(10)
,`Description` longtext
,`ReceiptNo` varchar(30)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`Balance` double(19,2)
,`Date` date
,`Time` datetime
,`Username` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_fee_outstading_view_rpt`
-- (See below for the actual view)
--
CREATE TABLE `student_fee_outstading_view_rpt` (
`StudentID` int(11)
,`FullName` varchar(161)
,`CurrentClassID` int(11)
,`CurrentClassName` varchar(150)
,`SubClassID` varchar(30)
,`AccountNo` int(11)
,`Stamp` varchar(10)
,`ReceiptID` longtext
,`CouponID` varchar(100)
,`BillingDate` date
,`Description` longtext
,`ReceiptNo` varchar(30)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`PmtStartDate` date
,`LastDate` date
,`Time` datetime
,`Username` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_fee_outstading_view_rpt_0`
-- (See below for the actual view)
--
CREATE TABLE `student_fee_outstading_view_rpt_0` (
`StudentID` int(11)
,`FullName` varchar(161)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`CurrentClassID` int(11)
,`CurrentClassName` varchar(150)
,`Stamp` varchar(10)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`Balance` double(19,2)
,`LastDate` date
,`CouponID` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_fee_outstading_view_rpt_1`
-- (See below for the actual view)
--
CREATE TABLE `student_fee_outstading_view_rpt_1` (
`StudentID` int(11)
,`FullName` varchar(161)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`CurrentClassID` int(11)
,`CurrentClassName` varchar(150)
,`Stamp` varchar(10)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`Balance` double(19,2)
,`LastDate` date
,`CouponID` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_fee_view`
-- (See below for the actual view)
--
CREATE TABLE `student_fee_view` (
`StudentID` int(11)
,`SubClassID` varchar(30)
,`CouponID` longtext
,`AccountNo` int(11)
,`Stamp` varchar(10)
,`Description` longtext
,`ReceiptNo` varchar(30)
,`Dr` float
,`Cr` float
,`Date` date
,`Time` datetime
,`Username` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_fee_view_1`
-- (See below for the actual view)
--
CREATE TABLE `student_fee_view_1` (
`StudentID` int(11)
,`SubClassID` varchar(30)
,`AccountNo` int(11)
,`CouponID` longtext
,`Stamp` varchar(10)
,`Description` longtext
,`ReceiptNo` varchar(30)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`Date` date
,`Time` datetime
,`Username` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_fee_view_2`
-- (See below for the actual view)
--
CREATE TABLE `student_fee_view_2` (
`StudentID` int(11)
,`FullName` longtext
,`TelNo` varchar(30)
,`SubClassID` varchar(30)
,`AccountNo` int(11)
,`CouponID` longtext
,`Stamp` varchar(10)
,`Description` longtext
,`ReceiptNo` varchar(30)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`Date` date
,`Time` datetime
,`Username` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_gender_chart`
-- (See below for the actual view)
--
CREATE TABLE `student_gender_chart` (
`SubCurrentClassID` int(11)
,`SubCurrentClassName` varchar(150)
,`Male` bigint(21)
,`Female` bigint(21)
,`TPop` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_gen_pop`
-- (See below for the actual view)
--
CREATE TABLE `student_gen_pop` (
`StudentID` varchar(30)
,`FullName` varchar(161)
,`Gender` varchar(10)
,`DOB` date
,`BoarderStats` varchar(15)
,`CourseID` int(11)
,`Course` varchar(100)
,`SubCurrentClassID` int(11)
,`SubCurrentClassName` varchar(150)
,`Date` date
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_gen_pop_0`
-- (See below for the actual view)
--
CREATE TABLE `student_gen_pop_0` (
`SubCurrentClassID` int(11)
,`SubCurrentClassName` varchar(150)
,`TPop` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_gen_pop_all`
-- (See below for the actual view)
--
CREATE TABLE `student_gen_pop_all` (
`SubCurrentClassID` int(11)
,`SubCurrentClassName` varchar(150)
,`Male` bigint(21)
,`Female` bigint(21)
,`TPop` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_gen_pop_female`
-- (See below for the actual view)
--
CREATE TABLE `student_gen_pop_female` (
`SubCurrentClassID` int(11)
,`SubCurrentClassName` varchar(150)
,`Gender` varchar(10)
,`Pop` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_gen_pop_male`
-- (See below for the actual view)
--
CREATE TABLE `student_gen_pop_male` (
`SubCurrentClassID` int(11)
,`SubCurrentClassName` varchar(150)
,`Gender` varchar(10)
,`Pop` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `student_id`
--

CREATE TABLE `student_id` (
  `StudentID` varchar(120) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_import`
--

CREATE TABLE `student_import` (
  `YEAR` int(4) DEFAULT NULL,
  `CODE` int(4) DEFAULT NULL,
  `STDID` varchar(7) NOT NULL DEFAULT '',
  `FIRSTNAME` varchar(50) DEFAULT NULL,
  `LAST NAME` varchar(22) DEFAULT NULL,
  `GENDER` varchar(7) DEFAULT NULL,
  `DOB` varchar(8) DEFAULT NULL,
  `course id` varchar(3) DEFAULT NULL,
  `classid` varchar(3) DEFAULT NULL,
  `ADMINDATE` varchar(8) DEFAULT NULL,
  `boarding` varchar(3) DEFAULT NULL,
  `LAST SCHOOL` varchar(2) DEFAULT NULL,
  `RESULT` varchar(2) DEFAULT NULL,
  `HOUSEID` int(1) DEFAULT NULL,
  `FATHER` varchar(2) DEFAULT NULL,
  `MOTHER` varchar(2) DEFAULT NULL,
  `OCCUPATION` varchar(2) DEFAULT NULL,
  `TELNO` int(3) DEFAULT NULL,
  `ADDRESS` varchar(2) DEFAULT NULL,
  `USERNAME` varchar(4) DEFAULT NULL,
  `DATE` varchar(8) DEFAULT NULL,
  `TIME` varchar(8) DEFAULT NULL,
  `STATUS` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_list`
--

CREATE TABLE `student_list` (
  `StudentID` varchar(255) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `ClassrmID` varchar(20) NOT NULL,
  `ClassrmName` varchar(20) NOT NULL,
  `CreatedBy` varchar(150) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_main`
--

CREATE TABLE `student_main` (
  `Year` int(11) NOT NULL,
  `Code` int(11) NOT NULL,
  `StudentID` varchar(20) NOT NULL,
  `FirstName` varchar(80) NOT NULL,
  `LastName` varchar(80) NOT NULL,
  `Gender` varchar(10) NOT NULL,
  `DOB` date NOT NULL,
  `STelNo` varchar(15) NOT NULL,
  `CourseID` int(11) NOT NULL,
  `ClassAdmiitted` int(11) NOT NULL,
  `AdmissionDate` date NOT NULL,
  `BoarderStats` varchar(15) NOT NULL,
  `LastSchool` varchar(180) NOT NULL,
  `AgregateResult` int(11) NOT NULL,
  `HouseID` int(11) NOT NULL,
  `FatherName` varchar(150) NOT NULL,
  `MotherName` varchar(150) NOT NULL,
  `TelNo` varchar(15) NOT NULL,
  `Email` longtext NOT NULL,
  `Occupation` varchar(150) NOT NULL,
  `Address` varchar(225) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Status` int(1) NOT NULL,
  `BranchID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_main_view`
-- (See below for the actual view)
--
CREATE TABLE `student_main_view` (
`Year` int(11)
,`Code` int(11)
,`StudentID` varchar(20)
,`FirstName` varchar(80)
,`LastName` varchar(80)
,`FullName` varchar(161)
,`Gender` varchar(10)
,`DOB` date
,`CourseID` int(11)
,`CourseName` varchar(100)
,`ClassAdmiitted` int(11)
,`CurrentClass` varchar(150)
,`AdmissionDate` date
,`BoarderStats` varchar(15)
,`LastSchool` varchar(180)
,`STelNo` varchar(15)
,`AgregateResult` int(11)
,`FatherName` varchar(150)
,`MotherName` varchar(150)
,`TelNo` varchar(15)
,`Occupation` varchar(150)
,`Address` varchar(225)
,`HouseID` int(11)
,`House` varchar(50)
,`Email` longtext
,`Date` date
,`Time` datetime
,`Username` varchar(20)
,`Status` int(1)
,`BranchID` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `student_promo`
--

CREATE TABLE `student_promo` (
  `StudentID` varchar(30) NOT NULL,
  `PreviousClass` int(11) NOT NULL,
  `PromoClass` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Time` date NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_promo_hist`
--

CREATE TABLE `student_promo_hist` (
  `CouponNo` varchar(30) NOT NULL,
  `StudentID` varchar(30) NOT NULL,
  `PreviouClass` varchar(10) NOT NULL,
  `PromoClass` varchar(10) NOT NULL,
  `PromStatus` int(11) NOT NULL,
  `Description` longtext NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_promo_view`
-- (See below for the actual view)
--
CREATE TABLE `student_promo_view` (
`StudentID` varchar(30)
,`PreviousClass` int(11)
,`PromoClass` int(11)
,`SubClassName` varchar(150)
,`Date` date
,`Time` date
,`Username` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_promo_view_empty`
-- (See below for the actual view)
--
CREATE TABLE `student_promo_view_empty` (
`PromoClass` int(11)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_promo_view_empty_0`
-- (See below for the actual view)
--
CREATE TABLE `student_promo_view_empty_0` (
`CourseID` int(11)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`Enroll` int(11)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `student_register`
--

CREATE TABLE `student_register` (
  `StudentID` varchar(80) NOT NULL,
  `SubClassID` int(11) NOT NULL,
  `Attendance` int(11) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_register_subject`
--

CREATE TABLE `student_register_subject` (
  `StudentID` varchar(150) NOT NULL,
  `SubClassID` int(11) NOT NULL,
  `SubjectID` int(11) NOT NULL,
  `Attendance` int(11) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_register_subject_view`
-- (See below for the actual view)
--
CREATE TABLE `student_register_subject_view` (
`StudentID` varchar(150)
,`FullName` varchar(161)
,`SubClassID` int(11)
,`CourseName` varchar(100)
,`SubClassName` varchar(150)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`AttendanceID` int(11)
,`Attendance` varchar(7)
,`Username` varchar(20)
,`Uname` varchar(180)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_register_view`
-- (See below for the actual view)
--
CREATE TABLE `student_register_view` (
`StudentID` varchar(80)
,`FullName` varchar(161)
,`SubClassID` int(11)
,`Course` varchar(100)
,`SubClassName` varchar(150)
,`AttendanceID` int(11)
,`Attendance` varchar(7)
,`Username` varchar(20)
,`Uname` varchar(180)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `student_terminal_report`
--

CREATE TABLE `student_terminal_report` (
  `CouponNo` varchar(50) NOT NULL,
  `Title` varchar(150) NOT NULL,
  `StudentID` varchar(20) NOT NULL,
  `FullName` varchar(150) NOT NULL,
  `Gender` varchar(10) NOT NULL,
  `CourseID` int(11) NOT NULL,
  `CourseName` varchar(50) NOT NULL,
  `SubClassID` int(11) NOT NULL,
  `SubClassName` varchar(30) NOT NULL,
  `SubjectCatID` varchar(10) NOT NULL,
  `SubjectCatNm` varchar(50) NOT NULL,
  `SubjectID` int(11) NOT NULL,
  `SubjectName` varchar(60) NOT NULL,
  `CWScore_0` float NOT NULL,
  `CWScore` float NOT NULL,
  `ExScore` float NOT NULL,
  `ExScore_0` float NOT NULL,
  `Total` float NOT NULL,
  `Position` int(11) NOT NULL,
  `Population` int(11) NOT NULL,
  `ClassTotal` float NOT NULL,
  `ClassAvg` int(11) NOT NULL,
  `SubjectCount` int(11) NOT NULL,
  `TotalMarks` float NOT NULL,
  `StudentAvg` float NOT NULL,
  `Username` varchar(15) NOT NULL,
  `StaffName` varchar(80) NOT NULL,
  `ViewerID` varchar(15) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_terminal_report_temp`
--

CREATE TABLE `student_terminal_report_temp` (
  `CouponNo` varchar(70) NOT NULL,
  `Title` varchar(200) NOT NULL,
  `StudentID` varchar(50) NOT NULL,
  `FullName` varchar(200) NOT NULL,
  `Gender` varchar(10) NOT NULL,
  `CourseID` varchar(20) NOT NULL,
  `CourseName` varchar(100) NOT NULL,
  `SubClassID` varchar(20) NOT NULL,
  `SubClassName` varchar(80) NOT NULL,
  `SubjectCatID` varchar(20) NOT NULL,
  `SubjectCatNm` varchar(50) NOT NULL,
  `SubjectID` char(20) NOT NULL,
  `SubjectName` varchar(100) NOT NULL,
  `CWScore_0` float NOT NULL,
  `CWScore` float NOT NULL,
  `ExScore` float NOT NULL,
  `ExScore_0` int(11) NOT NULL,
  `Total` float NOT NULL,
  `Position` int(11) NOT NULL,
  `Population` int(11) NOT NULL,
  `ClassTotal` float NOT NULL,
  `ClassAvg` float NOT NULL,
  `SubjectCount` int(11) NOT NULL,
  `TotalMarks` float NOT NULL,
  `StudentAvg` float NOT NULL,
  `Username` varchar(15) NOT NULL,
  `StaffName` varchar(150) NOT NULL,
  `ViewerID` varchar(15) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt` (
`CouponNo` varchar(150)
,`Title` varchar(100)
,`StudentID` varchar(100)
,`FullName` varchar(161)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`TestType` varchar(200)
,`TestID` int(11)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`Score` float
,`Username` varchar(20)
,`FName` varchar(180)
,`StaffName` varchar(120)
,`Date` date
,`Time` datetime
,`Status` int(11)
,`ViewerID` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_class_test`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_class_test` (
`StudentID` varchar(100)
,`TestCategory` varchar(200)
,`TestID` int(11)
,`TestType` varchar(200)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`Score` float
,`Username` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_class_test_0`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_class_test_0` (
`StudentID` varchar(100)
,`TestCategory` varchar(200)
,`Percentage` double(19,2)
,`TestID` int(11)
,`TestType` varchar(200)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`Score` double(19,2)
,`Username` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_class_test_1`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_class_test_1` (
`StudentID` varchar(100)
,`TestCategory` varchar(200)
,`Percentage` double(19,2)
,`TestID` int(11)
,`TestType` varchar(200)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`Score` double(19,2)
,`CWScore` double(18,1)
,`Username` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_exams`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_exams` (
`StudentID` varchar(100)
,`TestCategory` varchar(200)
,`TestID` int(11)
,`TestType` varchar(200)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`Score` float
,`Username` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_exams_0`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_exams_0` (
`StudentID` varchar(100)
,`TestCategory` varchar(200)
,`Percentage` double(19,2)
,`TestID` int(11)
,`TestType` varchar(200)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`Score` double(19,2)
,`Username` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_exams_1`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_exams_1` (
`StudentID` varchar(100)
,`TestCategory` varchar(200)
,`Percentage` double(19,2)
,`TestID` int(11)
,`TestType` varchar(200)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`Score` double(19,2)
,`EXScore` double(18,1)
,`Username` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_fnal`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_fnal` (
`CouponNo` varchar(150)
,`Title` varchar(100)
,`StudentID` varchar(100)
,`FullName` varchar(161)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`CWPcntg` varchar(19)
,`CWScore_0` double(19,2)
,`CWScore` double(18,1)
,`Username` varchar(20)
,`ViewerID` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_fnal_0`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_fnal_0` (
`CouponNo` varchar(150)
,`Title` varchar(100)
,`StudentID` varchar(100)
,`FullName` varchar(161)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`CWPcntg` varchar(19)
,`CWScore_0` double(19,2)
,`CWScore` double(18,1)
,`ExPcntg` double(19,2)
,`ExScore` double(19,2)
,`ExScore_0` double(18,1)
,`Username` varchar(20)
,`ViewerID` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_fnal_0_ind`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_fnal_0_ind` (
`CouponNo` varchar(150)
,`Title` varchar(100)
,`StudentID` varchar(100)
,`FullName` varchar(161)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`CWPcntg` varchar(19)
,`CWScore_0` double(19,2)
,`CWScore` double(18,1)
,`ExPcntg` double(19,2)
,`ExScore` double(19,2)
,`ExScore_0` double(18,1)
,`Username` varchar(20)
,`ViewerID` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_fnal_1`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_fnal_1` (
`CouponNo` varchar(150)
,`Title` varchar(100)
,`StudentID` varchar(100)
,`FullName` varchar(161)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`CWScore_0` double(19,2)
,`CWScore` double(17,0)
,`ExScore` double(17,0)
,`ExScore_0` double(17,0)
,`Total` double(17,0)
,`Username` varchar(20)
,`ViewerID` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_fnal_1_ind`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_fnal_1_ind` (
`CouponNo` varchar(150)
,`Title` varchar(100)
,`StudentID` varchar(100)
,`FullName` varchar(161)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`CWScore_0` double(19,2)
,`CWScore` double(17,0)
,`ExScore` double(17,0)
,`ExScore_0` double(17,0)
,`Total` double(17,0)
,`Username` varchar(20)
,`ViewerID` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_fnal_2`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_fnal_2` (
`CouponNo` varchar(150)
,`Title` varchar(100)
,`StudentID` varchar(100)
,`FullName` varchar(161)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`SubjectCatID` int(11)
,`SubjectCatNm` varchar(100)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`CWScore_0` double(19,2)
,`CWScore` double(17,0)
,`ExScore` double(17,0)
,`ExScore_0` double(17,0)
,`Total` double(17,0)
,`Username` varchar(20)
,`StaffName` varchar(120)
,`ViewerID` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_fnal_2_ind`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_fnal_2_ind` (
`CouponNo` varchar(150)
,`Title` varchar(100)
,`StudentID` varchar(100)
,`FullName` varchar(161)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`SubjectCatID` int(11)
,`SubjectCatNm` varchar(100)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`CWScore_0` double(19,2)
,`CWScore` double(17,0)
,`ExScore` double(17,0)
,`ExScore_0` double(17,0)
,`Total` double(17,0)
,`Username` varchar(20)
,`StaffName` varchar(120)
,`ViewerID` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_fnal_3`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_fnal_3` (
`CouponNo` varchar(150)
,`Title` varchar(100)
,`StudentID` varchar(100)
,`FullName` varchar(161)
,`Gender` varchar(10)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`SubjectCatID` int(11)
,`SubjectCatNm` varchar(100)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`CWScore_0` double(19,2)
,`CWScore` double(17,0)
,`ExScore` double(17,0)
,`ExScore_0` double(17,0)
,`Total` double(17,0)
,`Population` bigint(21)
,`ClassTotal` double(19,2)
,`ClassAvg` double(17,0)
,`SubjectCount` bigint(21)
,`TotalMarks` double(19,2)
,`StudentAvg` double(17,0)
,`Username` varchar(20)
,`StaffName` varchar(120)
,`ViewerID` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_fnal_3_ind`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_fnal_3_ind` (
`CouponNo` varchar(150)
,`Title` varchar(100)
,`StudentID` varchar(100)
,`FullName` varchar(161)
,`Gender` varchar(10)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`SubjectCatID` int(11)
,`SubjectCatNm` varchar(100)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`CWScore_0` double(19,2)
,`CWScore` double(17,0)
,`ExScore` double(17,0)
,`ExScore_0` double(17,0)
,`Total` double(17,0)
,`Population` bigint(21)
,`ClassTotal` double(19,2)
,`ClassAvg` double(17,0)
,`SubjectCount` bigint(21)
,`TotalMarks` double(19,2)
,`StudentAvg` double(17,0)
,`Username` varchar(20)
,`StaffName` varchar(120)
,`ViewerID` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_fnal_class_avg`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_fnal_class_avg` (
`Population` bigint(21)
,`ClassTotal` double(19,2)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_fnal_details`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_fnal_details` (
`Population` bigint(21)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`ClassMax` double(17,0)
,`ClassMin` double(17,0)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_fnal_ind`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_fnal_ind` (
`CouponNo` varchar(150)
,`Title` varchar(100)
,`StudentID` varchar(100)
,`FullName` varchar(161)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`CWPcntg` varchar(19)
,`CWScore_0` double(19,2)
,`CWScore` double(18,1)
,`Username` varchar(20)
,`ViewerID` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_fnal_student_avg`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_fnal_student_avg` (
`StudentID` varchar(100)
,`GrandTotal` double(19,2)
,`SubjectCount` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_ind`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_ind` (
`CouponNo` varchar(150)
,`Title` varchar(100)
,`StudentID` varchar(100)
,`FullName` varchar(161)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`TestType` varchar(200)
,`TestID` int(11)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`Score` float
,`Username` varchar(20)
,`FName` varchar(180)
,`StaffName` varchar(120)
,`Date` date
,`Time` datetime
,`Status` int(11)
,`ViewerID` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_names`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_names` (
`CouponNo` varchar(150)
,`Title` varchar(100)
,`StudentID` varchar(100)
,`FullName` varchar(161)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`Category` varchar(200)
,`TestID` int(11)
,`TestType` varchar(200)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`Score` float
,`Username` varchar(20)
,`ViewerID` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_names_0`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_names_0` (
`CouponNo` varchar(150)
,`Title` varchar(100)
,`StudentID` varchar(100)
,`FullName` varchar(161)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`Username` varchar(20)
,`ViewerID` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_names_0_ind`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_names_0_ind` (
`CouponNo` varchar(150)
,`Title` varchar(100)
,`StudentID` varchar(100)
,`FullName` varchar(161)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`Username` varchar(20)
,`ViewerID` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_terminal_rpt_names_ind`
-- (See below for the actual view)
--
CREATE TABLE `student_terminal_rpt_names_ind` (
`CouponNo` varchar(150)
,`Title` varchar(100)
,`StudentID` varchar(100)
,`FullName` varchar(161)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`Category` varchar(200)
,`TestID` int(11)
,`TestType` varchar(200)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`Score` float
,`Username` varchar(20)
,`ViewerID` varchar(20)
);

-- --------------------------------------------------------

--
-- Table structure for table `student_test_remarks`
--

CREATE TABLE `student_test_remarks` (
  `StudentID` varchar(25) NOT NULL,
  `CouponNo` varchar(100) NOT NULL,
  `TotalScore` float NOT NULL,
  `Average` float NOT NULL,
  `ClassPosition` int(11) NOT NULL,
  `Conduct` longtext NOT NULL,
  `Interest` longtext NOT NULL,
  `FMaster` longtext NOT NULL,
  `HMaster` longtext NOT NULL,
  `SubClassID` varchar(20) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `BranchID` varchar(10) NOT NULL,
  `Status` int(11) NOT NULL,
  `Time` datetime NOT NULL,
  `UpdateTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_test_score`
--

CREATE TABLE `student_test_score` (
  `CouponNo` varchar(150) NOT NULL,
  `StudentID` varchar(100) NOT NULL,
  `SubClassID` int(11) NOT NULL,
  `TestType` varchar(10) NOT NULL,
  `TestID` int(11) NOT NULL,
  `SubjectID` int(11) NOT NULL,
  `Score` float NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_test_score_view`
-- (See below for the actual view)
--
CREATE TABLE `student_test_score_view` (
`CouponNo` varchar(150)
,`Title` varchar(100)
,`StudentID` varchar(100)
,`FullName` varchar(161)
,`CourseID` int(11)
,`CourseName` varchar(100)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`TestCategory` varchar(200)
,`TestType` varchar(200)
,`TestID` int(11)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`Score` float
,`Username` varchar(20)
,`FName` varchar(180)
,`StaffName` varchar(120)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_write_off_fee_balance`
-- (See below for the actual view)
--
CREATE TABLE `student_write_off_fee_balance` (
`StudentID` int(11)
,`SubClassID` varchar(30)
,`AccountNo` int(11)
,`Stamp` varchar(10)
,`CouponID` longtext
,`Description` longtext
,`ReceiptNo` varchar(30)
,`Dr` float
,`Cr` float
,`Date` date
,`ActiveDate` date
,`Time` datetime
,`Username` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_write_off_fee_balance_0`
-- (See below for the actual view)
--
CREATE TABLE `student_write_off_fee_balance_0` (
`StudentID` int(11)
,`Stamp` varchar(10)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`Balance` double(19,2)
,`ActiveDate` date
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_write_off_fee_balance_1`
-- (See below for the actual view)
--
CREATE TABLE `student_write_off_fee_balance_1` (
`StudentID` int(11)
,`FullName` varchar(161)
,`CurrentClassID` int(11)
,`CurrentClassName` varchar(150)
,`Stamp` varchar(10)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`Balance` double(19,2)
,`ActiveDate` date
);

-- --------------------------------------------------------

--
-- Table structure for table `subclass_form_master`
--

CREATE TABLE `subclass_form_master` (
  `StaffID` varchar(20) NOT NULL,
  `SubClassID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `subclass_form_master_view`
-- (See below for the actual view)
--
CREATE TABLE `subclass_form_master_view` (
`StaffID` varchar(20)
,`FullName` varchar(120)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`Date` date
,`Time` datetime
,`Username` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `subclass_master`
--

CREATE TABLE `subclass_master` (
  `StaffID` varchar(20) NOT NULL,
  `SubClassID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subject_main`
--

CREATE TABLE `subject_main` (
  `CategoryID` int(11) NOT NULL,
  `SubjectID` int(11) NOT NULL,
  `SubjectName` varchar(100) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `subject_main_view`
-- (See below for the actual view)
--
CREATE TABLE `subject_main_view` (
`CategoryID` int(11)
,`CategoryName` varchar(100)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`Date` date
,`Time` datetime
,`Username` varchar(15)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `subj_category`
--

CREATE TABLE `subj_category` (
  `CategoryID` int(11) NOT NULL,
  `CategoryName` varchar(100) NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sub_class_id`
--

CREATE TABLE `sub_class_id` (
  `SubClassID` int(11) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `sub_class_id_view`
-- (See below for the actual view)
--
CREATE TABLE `sub_class_id_view` (
`SubClassID` int(11)
,`SubClassName` varchar(150)
,`CategoryID` int(11)
,`CategoryName` varchar(100)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`Username` varchar(20)
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `sub_class_main`
--

CREATE TABLE `sub_class_main` (
  `CourseID` int(11) NOT NULL,
  `SubClassID` int(11) NOT NULL,
  `SubClassName` varchar(150) NOT NULL,
  `Enroll` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `sub_class_main_view`
-- (See below for the actual view)
--
CREATE TABLE `sub_class_main_view` (
`CourseID` int(11)
,`ClassName` varchar(100)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`Enroll` int(11)
,`Date` date
,`Time` datetime
,`Username` varchar(15)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `sub_class_subject`
--

CREATE TABLE `sub_class_subject` (
  `CourseID` int(11) NOT NULL,
  `SubClassID` int(11) NOT NULL,
  `SubjectID` int(11) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Time` datetime NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `sub_class_subject_view`
-- (See below for the actual view)
--
CREATE TABLE `sub_class_subject_view` (
`SubClassID` int(11)
,`SubClassName` varchar(150)
,`CategoryID` int(11)
,`CategoryName` varchar(100)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`Username` varchar(20)
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `table 66`
--

CREATE TABLE `table 66` (
  `YEAR` int(4) DEFAULT NULL,
  `CODE` int(4) DEFAULT NULL,
  `STDID` varchar(7) DEFAULT NULL,
  `FIRST NAME` varchar(19) DEFAULT NULL,
  `LAST NAME` varchar(22) DEFAULT NULL,
  `GENDER` varchar(7) DEFAULT NULL,
  `DOB` varchar(8) DEFAULT NULL,
  `course id` varchar(3) DEFAULT NULL,
  `class` varchar(12) DEFAULT NULL,
  `classid` varchar(3) DEFAULT NULL,
  `ADMINDATE` varchar(8) DEFAULT NULL,
  `boarding` varchar(3) DEFAULT NULL,
  `LAST SCHOOL` varchar(2) DEFAULT NULL,
  `RESULT` varchar(2) DEFAULT NULL,
  `HOUSEID` int(1) DEFAULT NULL,
  `FATHER` varchar(2) DEFAULT NULL,
  `MOTHER` varchar(2) DEFAULT NULL,
  `OCCUPATION` varchar(2) DEFAULT NULL,
  `TELNO` int(3) DEFAULT NULL,
  `ADDRESS` varchar(2) DEFAULT NULL,
  `USERNAME` varchar(4) DEFAULT NULL,
  `DATE` varchar(8) DEFAULT NULL,
  `TIME` varchar(8) DEFAULT NULL,
  `STATUS` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tax_components`
--

CREATE TABLE `tax_components` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL,
  `rate` decimal(5,2) NOT NULL,
  `applies_on` enum('base','subtotal') NOT NULL,
  `sort_order` int(11) NOT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT 1,
  `effective_date` date NOT NULL,
  `Username` varchar(15) NOT NULL DEFAULT 'system',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_mapping`
--

CREATE TABLE `teacher_mapping` (
  `Code` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temp_class_promotion`
--

CREATE TABLE `temp_class_promotion` (
  `StudentID` varchar(20) NOT NULL,
  `FullName` longtext NOT NULL,
  `SubClassID` int(11) NOT NULL,
  `PromStatus` int(11) NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temp_class_subj_map`
--

CREATE TABLE `temp_class_subj_map` (
  `CategoryID` int(11) NOT NULL,
  `SubjectID` int(11) NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `temp_class_subj_map_view`
-- (See below for the actual view)
--
CREATE TABLE `temp_class_subj_map_view` (
`CategoryID` int(11)
,`CategoryName` varchar(100)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`Username` varchar(15)
,`Time` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `temp_gender_data_chart`
--

CREATE TABLE `temp_gender_data_chart` (
  `SubClassID` int(11) NOT NULL,
  `SubClassName` varchar(50) NOT NULL,
  `TPop` int(11) NOT NULL,
  `Female` int(11) NOT NULL,
  `Male` int(11) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temp_general_student_billing`
--

CREATE TABLE `temp_general_student_billing` (
  `StudentID` varchar(20) NOT NULL,
  `FullName` varchar(120) NOT NULL,
  `SubClassID` int(11) NOT NULL,
  `SubClassName` varchar(30) NOT NULL,
  `Status` int(11) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `BranchID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `temp_general_student_billing_view`
-- (See below for the actual view)
--
CREATE TABLE `temp_general_student_billing_view` (
`StudentID` varchar(20)
,`FullName` varchar(120)
,`SubClassID` int(11)
,`SubClassName` varchar(30)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`Amount` float
,`Status` int(11)
,`Username` varchar(20)
,`Date` date
,`Time` datetime
,`BranchID` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `temp_general_student_billing_view_0`
-- (See below for the actual view)
--
CREATE TABLE `temp_general_student_billing_view_0` (
`StudentID` varchar(20)
,`FullName` varchar(120)
,`SubClassID` int(11)
,`SubClassName` varchar(30)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`Amount` float
,`Status` int(11)
,`Username` varchar(20)
,`Date` date
,`Time` datetime
,`BranchID` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `temp_general_student_billing_view_1`
-- (See below for the actual view)
--
CREATE TABLE `temp_general_student_billing_view_1` (
`StudentID` varchar(20)
,`FullName` varchar(120)
,`SubClassID` int(11)
,`SubClassName` varchar(30)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`Amount` double(19,2)
,`Status` int(11)
,`Username` varchar(20)
,`Date` date
,`Time` datetime
,`BranchID` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `temp_ind_std_bill_account`
--

CREATE TABLE `temp_ind_std_bill_account` (
  `StudentID` varchar(30) NOT NULL,
  `FullName` varchar(200) NOT NULL,
  `SubClassID` int(11) NOT NULL,
  `SubClassName` varchar(100) NOT NULL,
  `AccountID` int(11) NOT NULL,
  `AccountName` varchar(50) NOT NULL,
  `Amount` float NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `temp_mainbl_new_consignee`
-- (See below for the actual view)
--
CREATE TABLE `temp_mainbl_new_consignee` (
`ConsigenmentID` varchar(100)
,`ShipperID` int(11)
,`ShipperName` varchar(150)
,`VesselName` varchar(80)
,`ContWeight` double(19,2)
,`ContainerNo` varchar(50)
,`ContainerSize` varchar(15)
,`MainBL` varchar(100)
,`Username` varchar(20)
,`Time` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `temp_manifestation_breakdown`
--

CREATE TABLE `temp_manifestation_breakdown` (
  `ConsignmentID` int(11) NOT NULL,
  `MainBL` varchar(30) NOT NULL,
  `ContainerNo` varchar(50) NOT NULL,
  `HouseBL` varchar(30) NOT NULL,
  `CosigneeID` int(11) NOT NULL,
  `Cosignee2_ID` int(11) NOT NULL,
  `Description` longtext NOT NULL,
  `ItemType` varchar(15) NOT NULL,
  `VIN` longtext NOT NULL,
  `OtherInfo` longtext NOT NULL,
  `Weight` float NOT NULL,
  `Package` varchar(10) NOT NULL,
  `Unit` varchar(7) NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `temp_manifestation_breakdown_total_weight_view`
-- (See below for the actual view)
--
CREATE TABLE `temp_manifestation_breakdown_total_weight_view` (
`ConsignmentID` int(11)
,`MainBL` varchar(30)
,`TWeight` double(20,3)
,`Username` varchar(15)
,`ContWeight` double(19,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `temp_manifestation_breakdown_total_weight_view_0`
-- (See below for the actual view)
--
CREATE TABLE `temp_manifestation_breakdown_total_weight_view_0` (
`ConsignmentID` int(11)
,`MainBL` varchar(30)
,`TWeight` double(20,3)
,`Username` varchar(15)
,`ContWeight` double(19,2)
,`BLWeight` double(19,2)
,`RemWieght` double(19,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `temp_manifestation_breakdown_view`
-- (See below for the actual view)
--
CREATE TABLE `temp_manifestation_breakdown_view` (
`ConsignmentID` int(11)
,`MainBL` varchar(30)
,`HouseBL` varchar(30)
,`ContainerNo` varchar(50)
,`HBLNo` varchar(4)
,`HDLID` varchar(1)
,`CosigneeID` int(11)
,`FullName` longtext
,`Cosignee2_ID` int(11)
,`Weight` float
,`Package` varchar(10)
,`Description` longtext
,`ItemType` varchar(15)
,`VIN` longtext
,`OtherInfo` longtext
,`Unit` varchar(7)
,`Username` varchar(15)
,`Time` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `temp_manifestation_breakdown_view_0`
-- (See below for the actual view)
--
CREATE TABLE `temp_manifestation_breakdown_view_0` (
`ConsignmentID` int(11)
,`MainBL` varchar(30)
,`ContainerNo` varchar(50)
,`TWeight` double(19,2)
,`Username` varchar(15)
,`time` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `temp_other_invoice`
--

CREATE TABLE `temp_other_invoice` (
  `ClientID` varchar(30) NOT NULL,
  `AccountNo` int(11) NOT NULL,
  `Amount` float NOT NULL,
  `Description` longtext NOT NULL,
  `GetFund` float NOT NULL,
  `VAT` float NOT NULL,
  `Username` varchar(10) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temp_other_invoice_non_manifest`
--

CREATE TABLE `temp_other_invoice_non_manifest` (
  `ClientID` varchar(30) NOT NULL,
  `AccountNo` int(11) NOT NULL,
  `Amount` float NOT NULL,
  `TaxStatus` varchar(10) NOT NULL,
  `GetFund` float NOT NULL,
  `NHIL` double NOT NULL,
  `Covid` double NOT NULL,
  `VAT` float NOT NULL,
  `Username` varchar(10) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `temp_other_invoice_non_manifest_view`
-- (See below for the actual view)
--
CREATE TABLE `temp_other_invoice_non_manifest_view` (
`ClientID` varchar(30)
,`AccountNo` int(11)
,`AccountName` varchar(150)
,`Amount` float
,`TaxStatus` varchar(10)
,`GetFund` float
,`NHIL` double
,`Covid` double
,`GetFundVal` double(19,2)
,`NHILVal` double(19,2)
,`CovidVal` double(19,2)
,`SubTotal` double(19,2)
,`VAT` float
,`Username` varchar(10)
,`Time` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `temp_other_invoice_non_manifest_view_0`
-- (See below for the actual view)
--
CREATE TABLE `temp_other_invoice_non_manifest_view_0` (
`ClientID` varchar(30)
,`AccountNo` int(11)
,`AccountName` varchar(150)
,`Amount` float
,`TaxStatus` varchar(10)
,`GetFund` float
,`GetFundVal` double(19,2)
,`SubTotal` double(19,2)
,`VAT` float
,`VATVal` double(19,2)
,`GTotal` double(19,2)
,`Username` varchar(10)
,`Time` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `temp_other_invoice_view`
-- (See below for the actual view)
--
CREATE TABLE `temp_other_invoice_view` (
`ClientID` varchar(30)
,`AccountNo` int(11)
,`AccountName` varchar(150)
,`Amount` float
,`Description` longtext
,`GetFund` float
,`GetFundVal` double(19,2)
,`SubTotal` double(19,2)
,`VAT` float
,`Username` varchar(10)
,`Time` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `temp_other_invoice_view_0`
-- (See below for the actual view)
--
CREATE TABLE `temp_other_invoice_view_0` (
`ClientID` varchar(30)
,`AccountNo` int(11)
,`AccountName` varchar(150)
,`Amount` float
,`Description` longtext
,`GetFund` float
,`GetFundVal` double(19,2)
,`SubTotal` double(19,2)
,`VAT` float
,`VATVal` double(19,2)
,`GTotal` double(19,2)
,`Username` varchar(10)
,`Time` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `temp_staff_class_subj_mapp`
--

CREATE TABLE `temp_staff_class_subj_mapp` (
  `StaffID` varchar(15) NOT NULL,
  `ClassID` int(11) NOT NULL,
  `SubjectID` int(11) NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `temp_staff_class_subj_mapp_view`
-- (See below for the actual view)
--
CREATE TABLE `temp_staff_class_subj_mapp_view` (
`StaffID` varchar(15)
,`FullName` varchar(120)
,`ClassID` int(11)
,`SubClassName` varchar(150)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`CategoryName` varchar(100)
,`Time` datetime
,`Username` varchar(15)
);

-- --------------------------------------------------------

--
-- Table structure for table `temp_student_admission_fee`
--

CREATE TABLE `temp_student_admission_fee` (
  `StudentID` varchar(40) NOT NULL,
  `CourseID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temp_student_register`
--

CREATE TABLE `temp_student_register` (
  `StudentID` varchar(100) NOT NULL,
  `SubClassID` int(11) NOT NULL,
  `Attendance` int(11) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `temp_student_register_view`
-- (See below for the actual view)
--
CREATE TABLE `temp_student_register_view` (
`StudentID` varchar(100)
,`FullName` varchar(161)
,`SubClassID` int(11)
,`SubCurrentClassID` int(11)
,`SubCurrentClassName` varchar(150)
,`Attendance` int(11)
,`Username` varchar(20)
,`Date` date
,`Time` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `temp_student_test_score`
--

CREATE TABLE `temp_student_test_score` (
  `StudentID` varchar(50) NOT NULL,
  `SubClassID` int(11) NOT NULL,
  `SubjectID` int(11) NOT NULL,
  `TestID` int(11) NOT NULL,
  `Score` float NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `temp_student_test_score_view`
-- (See below for the actual view)
--
CREATE TABLE `temp_student_test_score_view` (
`StudentID` varchar(50)
,`FullName` varchar(161)
,`SubClassID` int(11)
,`SubClassName` varchar(150)
,`SubjectID` int(11)
,`SubjectName` varchar(100)
,`TestID` int(11)
,`TestName` varchar(200)
,`MaxScore` float
,`Score` float
,`Username` varchar(20)
,`Time` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `temp_subject_register`
--

CREATE TABLE `temp_subject_register` (
  `StudentID` varchar(150) NOT NULL,
  `FullName` varchar(150) NOT NULL,
  `SubClassID` int(11) NOT NULL,
  `SubClassName` varchar(50) NOT NULL,
  `SubjectID` varchar(100) NOT NULL,
  `Attendance` int(11) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temp_sub_class_subj_map`
--

CREATE TABLE `temp_sub_class_subj_map` (
  `SubjCatName` varchar(150) NOT NULL,
  `SubjectID` int(11) NOT NULL,
  `SubjectName` varchar(150) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temp_transaction_reversal`
--

CREATE TABLE `temp_transaction_reversal` (
  `AccountID` int(11) NOT NULL,
  `SubAccountID` int(11) NOT NULL,
  `Mode` varchar(5) NOT NULL,
  `ReceiptNo` varchar(30) NOT NULL,
  `Dr` float NOT NULL,
  `Cr` float NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Username` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `temp_transaction_reversal_view`
-- (See below for the actual view)
--
CREATE TABLE `temp_transaction_reversal_view` (
`AccountID` int(11)
,`AccountName` varchar(150)
,`SubAccountID` int(11)
,`Mode` varchar(5)
,`ReceiptNo` varchar(30)
,`Dr` float
,`Cr` float
,`Date` date
,`Time` datetime
,`Username` varchar(15)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `temp_transaction_reversal_view_0`
-- (See below for the actual view)
--
CREATE TABLE `temp_transaction_reversal_view_0` (
`AccountID` int(11)
,`AccountName` varchar(150)
,`SubAccountID` int(11)
,`SubAccountName` varchar(150)
,`Mode` varchar(5)
,`ReceiptNo` varchar(30)
,`Dr` float
,`Cr` float
,`Date` date
,`Time` datetime
,`Username` varchar(15)
);

-- --------------------------------------------------------

--
-- Table structure for table `testconduct`
--

CREATE TABLE `testconduct` (
  `ID` int(11) NOT NULL,
  `Remarks` longtext NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `testfmaster`
--

CREATE TABLE `testfmaster` (
  `ID` int(11) NOT NULL,
  `Remarks` longtext NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `testhmaster`
--

CREATE TABLE `testhmaster` (
  `ID` int(11) NOT NULL,
  `Remarks` longtext NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `testinterest`
--

CREATE TABLE `testinterest` (
  `ID` int(11) NOT NULL,
  `Interest` longtext NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `test_score`
--

CREATE TABLE `test_score` (
  `ID` int(11) NOT NULL,
  `MaxScore` float NOT NULL,
  `MinScore` float NOT NULL,
  `Grade` varchar(5) NOT NULL,
  `Value` int(11) NOT NULL,
  `Remarks` varchar(200) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `test_setup`
--

CREATE TABLE `test_setup` (
  `CouponNo` varchar(120) NOT NULL,
  `Type` varchar(200) NOT NULL,
  `TestID` int(11) NOT NULL,
  `TestName` varchar(200) NOT NULL,
  `MaxScore` float NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `test_setup_view`
-- (See below for the actual view)
--
CREATE TABLE `test_setup_view` (
`CouponNo` varchar(120)
,`Title` varchar(100)
,`Type` varchar(200)
,`TestID` int(11)
,`TestName` varchar(200)
,`MaxScore` float
,`Username` varchar(20)
,`Date` date
,`Time` datetime
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `test_type`
--

CREATE TABLE `test_type` (
  `TypeID` int(11) NOT NULL,
  `TypeName` varchar(100) NOT NULL,
  `Percentage` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `ticket_terminal_rpt_unauth`
-- (See below for the actual view)
--
CREATE TABLE `ticket_terminal_rpt_unauth` (
`TicketNo` varchar(15)
,`SubClassID` varchar(10)
,`StaffID` varchar(20)
,`StudentID` varchar(40)
,`FullName` varchar(161)
,`CouponNo` varchar(100)
,`Validation` int(11)
,`Date` date
,`Time` datetime
,`Username` varchar(15)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `tracked_shipment`
-- (See below for the actual view)
--
CREATE TABLE `tracked_shipment` (
`ConsignmentID` int(11)
,`CarrierID` int(11)
,`Rotation` varchar(30)
,`ShipperID` int(11)
,`VesselName` varchar(80)
,`VoyageNo` varchar(80)
,`SealNo` varchar(50)
,`ETA` date
,`ArrivalStatus` varchar(19)
,`BL` varchar(50)
,`ContainerNo` varchar(30)
,`ContainerSize` varchar(15)
,`ContWeight` float
,`Charges` float
,`AgentContact` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `tracked_shipment_0`
-- (See below for the actual view)
--
CREATE TABLE `tracked_shipment_0` (
`ConsignmentID` int(11)
,`CarrierID` int(11)
,`Rotation` varchar(30)
,`ShipperID` int(11)
,`VesselName` varchar(80)
,`VoyageNo` varchar(80)
,`SealNo` varchar(50)
,`ETA` date
,`ETA_Days` int(7)
,`ArrivalStatus` varchar(19)
,`BL` varchar(50)
,`ContainerNo` varchar(30)
,`ContainerSize` varchar(15)
,`ContWeight` float
,`Charges` float
,`AgentContact` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `t_date`
--

CREATE TABLE `t_date` (
  `ActiveDate` date NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `unassign_student_sub_class`
-- (See below for the actual view)
--
CREATE TABLE `unassign_student_sub_class` (
`Year` int(11)
,`Code` int(11)
,`StudentID` varchar(20)
,`FullName` varchar(161)
,`Gender` varchar(10)
,`DOB` date
,`CourseID` int(11)
,`CourseName` varchar(100)
,`ClassAdmiitted` int(11)
,`AdmissionDate` date
,`BoarderStats` varchar(15)
,`LastSchool` varchar(180)
,`AgregateResult` int(11)
,`HouseID` int(11)
,`FatherName` varchar(150)
,`MotherName` varchar(150)
,`TelNo` varchar(15)
,`Occupation` varchar(150)
,`Address` varchar(225)
,`Date` date
,`Time` datetime
,`Username` varchar(20)
,`Status` int(1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `unauthorise_fee_charges`
-- (See below for the actual view)
--
CREATE TABLE `unauthorise_fee_charges` (
`StudentID` int(11)
,`SubClassID` varchar(30)
,`AccountNo` int(11)
,`Stamp` varchar(10)
,`CouponID` longtext
,`Description` longtext
,`ReceiptNo` varchar(30)
,`Dr` float
,`Cr` float
,`Date` date
,`Time` datetime
,`Username` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `unauthorise_fee_charges_0`
-- (See below for the actual view)
--
CREATE TABLE `unauthorise_fee_charges_0` (
`StudentID` int(11)
,`SubClassID` varchar(30)
,`Stamp` varchar(10)
,`CouponID` longtext
,`Description` longtext
,`ReceiptNo` varchar(30)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`Date` date
,`Username` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `unauthorise_fee_charges_1`
-- (See below for the actual view)
--
CREATE TABLE `unauthorise_fee_charges_1` (
`StudentID` int(11)
,`FullName` varchar(161)
,`SubClassID` varchar(30)
,`SubClassName` varchar(150)
,`Stamp` varchar(10)
,`CouponID` longtext
,`Description` longtext
,`ReceiptNo` varchar(30)
,`TDr` double(19,2)
,`TCr` double(19,2)
,`Date` date
,`Username` varchar(20)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `unauthorise_gl_transaction`
-- (See below for the actual view)
--
CREATE TABLE `unauthorise_gl_transaction` (
`ControlID` int(11)
,`CategoryID` int(11)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`SubAccountID` int(11)
,`Mode` varchar(5)
,`TType` varchar(5)
,`ReceiptNo` varchar(30)
,`Dr` double(19,2)
,`Cr` double(19,2)
,`Description` longtext
,`Date` date
,`Time` timestamp
,`Username` varchar(11)
,`Authorizer` varchar(15)
,`BranchID` int(11)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `unauthorise_pnl_transaction`
-- (See below for the actual view)
--
CREATE TABLE `unauthorise_pnl_transaction` (
`ControlID` int(11)
,`CategoryID` int(11)
,`AccountID` int(11)
,`AccountName` varchar(150)
,`SubAccountID` int(11)
,`SubAccountName` varchar(150)
,`Mode` varchar(5)
,`TType` varchar(5)
,`ReceiptNo` varchar(30)
,`Dr` double(19,2)
,`Cr` double(19,2)
,`Description` longtext
,`Type` varchar(15)
,`Date` date
,`Time` timestamp
,`Username` varchar(11)
,`Authorizer` varchar(15)
,`BranchID` int(11)
,`Status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `MemberID` varchar(30) NOT NULL,
  `Picture` longtext NOT NULL,
  `Created` datetime NOT NULL,
  `Modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `users_view`
-- (See below for the actual view)
--
CREATE TABLE `users_view` (
`MemberID` varchar(30)
,`FullName` varchar(100)
,`Picture` longtext
,`Created` datetime
,`Modified` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `user_auth`
--

CREATE TABLE `user_auth` (
  `Username` varchar(10) NOT NULL,
  `BasicConfig` int(11) NOT NULL,
  `ConsignmentRegister` int(11) NOT NULL,
  `GenerateInvoice` int(11) NOT NULL,
  `PaymentTransaction` int(11) NOT NULL,
  `GLTransaction` int(11) NOT NULL,
  `Disbursement` int(11) NOT NULL,
  `ConsignmentExpense` int(11) NOT NULL,
  `Transport` int(11) NOT NULL,
  `EditData` int(11) NOT NULL,
  `AssignConsignmentOfficer` int(11) NOT NULL,
  `DisbursementAnalysis` int(11) NOT NULL,
  `DisbursementRevenue` int(11) NOT NULL,
  `DisbursementOtherExpense` int(11) NOT NULL,
  `DisbursementApproval` int(11) NOT NULL,
  `ReverseTransaction` int(11) NOT NULL,
  `AccountingReport` int(11) NOT NULL,
  `CashExpenditure` int(11) NOT NULL,
  `TransportTrip` int(11) NOT NULL,
  `TransportExpense` int(11) NOT NULL,
  `PettyCash` int(11) NOT NULL,
  `CnsAwaitingClearance` int(11) NOT NULL,
  `PendingGateOutDashboard` int(11) NOT NULL,
  `VehicleHubDashboard` int(11) NOT NULL,
  `UserPrivilege` int(11) NOT NULL,
  `Hashing` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_expense_petty_cash`
--

CREATE TABLE `user_expense_petty_cash` (
  `Username` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_login_logs`
--

CREATE TABLE `user_login_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(30) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_login_logs_archive`
--

CREATE TABLE `user_login_logs_archive` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `waybill_main`
--

CREATE TABLE `waybill_main` (
  `id` int(11) NOT NULL,
  `Consignee` varchar(125) NOT NULL,
  `VehicleNo` varchar(15) NOT NULL,
  `DriverName` varchar(125) NOT NULL,
  `Port` varchar(25) NOT NULL,
  `DriverLicense` varchar(20) NOT NULL,
  `Package` longtext NOT NULL,
  `Description` longtext NOT NULL,
  `Quantity` int(11) NOT NULL,
  `WaybillDate` date NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Date` date NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure for view `active_account_receivable_view`
--
DROP TABLE IF EXISTS `active_account_receivable_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_account_receivable_view`  AS SELECT `active_account_receivable`.`AccountNo` AS `AccountNo`, `ledger_account`.`AccountName` AS `AccountName` FROM (`active_account_receivable` join `ledger_account` on(`active_account_receivable`.`AccountNo` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `active_bank_cash_view`
--
DROP TABLE IF EXISTS `active_bank_cash_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_bank_cash_view`  AS SELECT `active_bank_cash`.`AccountID` AS `AccountID`, `ledger_account`.`AccountName` AS `AccountName` FROM (`active_bank_cash` join `ledger_account` on(`active_bank_cash`.`AccountID` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `active_consignment_commodities`
--
DROP TABLE IF EXISTS `active_consignment_commodities`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_consignment_commodities`  AS SELECT `container_main`.`ConsignmentID` AS `ConsignmentID`, `container_main`.`BL` AS `BL`, `container_main`.`ConsigneeID` AS `ConsigneeID`, `container_main`.`ETA` AS `ETA`, `container_main`.`CmdtTypeID` AS `CmdtTypeID`, `container_main`.`ReleaseType` AS `ReleaseType`, `container_main`.`Destination` AS `Destination`, `container_main`.`BranchID` AS `BranchID`, `container_main`.`Date` AS `Date`, `container_main`.`Status` AS `Status` FROM `container_main` ;

-- --------------------------------------------------------

--
-- Structure for view `active_consignment_commodities_1`
--
DROP TABLE IF EXISTS `active_consignment_commodities_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_consignment_commodities_1`  AS SELECT `active_consignment_commodities`.`ConsignmentID` AS `ConsignmentID`, `active_consignment_commodities`.`BL` AS `BL`, `active_consignment_commodities`.`ConsigneeID` AS `ConsigneeID`, `active_consignment_commodities`.`ETA` AS `ETA`, `active_consignment_commodities`.`CmdtTypeID` AS `CmdtTypeID`, `commodity_type`.`TypeName` AS `CommodityType`, `active_consignment_commodities`.`ReleaseType` AS `ReleaseType`, `active_consignment_commodities`.`Destination` AS `Destination`, `active_consignment_commodities`.`BranchID` AS `BranchID`, `active_consignment_commodities`.`Date` AS `Date`, `active_consignment_commodities`.`Status` AS `Status` FROM (`active_consignment_commodities` join `commodity_type` on(`active_consignment_commodities`.`CmdtTypeID` = `commodity_type`.`TypeID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `active_consignment_commodities_2`
--
DROP TABLE IF EXISTS `active_consignment_commodities_2`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_consignment_commodities_2`  AS SELECT `active_consignment_commodities_1`.`ConsignmentID` AS `ConsignmentID`, `active_consignment_commodities_1`.`BL` AS `BL`, `active_consignment_commodities_1`.`ConsigneeID` AS `ConsigneeID`, `active_consignment_commodities_1`.`ETA` AS `ETA`, `active_consignment_commodities_1`.`CmdtTypeID` AS `CmdtTypeID`, `active_consignment_commodities_1`.`CommodityType` AS `CommodityType`, `active_consignment_commodities_1`.`ReleaseType` AS `ReleaseTypeID`, `container_release`.`ReleaseType` AS `ReleaseType`, `active_consignment_commodities_1`.`Destination` AS `Destination`, `active_consignment_commodities_1`.`BranchID` AS `BranchID`, `active_consignment_commodities_1`.`Date` AS `Date`, `active_consignment_commodities_1`.`Status` AS `Status` FROM (`active_consignment_commodities_1` join `container_release` on(`active_consignment_commodities_1`.`ReleaseType` = `container_release`.`ID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `active_ie_view`
--
DROP TABLE IF EXISTS `active_ie_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_ie_view`  AS SELECT `active_ie`.`AccountID` AS `AccountID`, `ledger_account`.`AccountName` AS `AccountName` FROM (`active_ie` join `ledger_account` on(`active_ie`.`AccountID` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `active_momo_view`
--
DROP TABLE IF EXISTS `active_momo_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_momo_view`  AS SELECT `active_momo`.`AccountNo` AS `AccountNo`, `ledger_account`.`AccountName` AS `AccountName` FROM (`active_momo` join `ledger_account` on(`active_momo`.`AccountNo` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `active_petty_cash_view`
--
DROP TABLE IF EXISTS `active_petty_cash_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_petty_cash_view`  AS SELECT DISTINCT `active_petty_cash`.`AccountNo` AS `AccountNo`, `journal_view_0`.`AccountName` AS `AccountName`, `journal_view_0`.`Dr` AS `Dr`, `journal_view_0`.`Cr` AS `Cr`, round(`journal_view_0`.`Dr` - `journal_view_0`.`Cr`,2) AS `Bal`, `active_petty_cash`.`Username` AS `Username` FROM (`active_petty_cash` join `journal_view_0` on(`active_petty_cash`.`AccountNo` = `journal_view_0`.`AccountID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `active_petty_cash_view_0`
--
DROP TABLE IF EXISTS `active_petty_cash_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_petty_cash_view_0`  AS SELECT `active_petty_cash_view`.`AccountNo` AS `AccountNo`, `active_petty_cash_view`.`AccountName` AS `AccountName`, round(sum(`active_petty_cash_view`.`Dr`),2) AS `TDr`, round(sum(`active_petty_cash_view`.`Cr`),2) AS `TCr`, round(sum(`active_petty_cash_view`.`Cr`) - sum(`active_petty_cash_view`.`Dr`),2) AS `TBal`, `active_petty_cash_view`.`Username` AS `Username` FROM `active_petty_cash_view` GROUP BY `active_petty_cash_view`.`AccountNo`, `active_petty_cash_view`.`Username` ;

-- --------------------------------------------------------

--
-- Structure for view `active_petty_view`
--
DROP TABLE IF EXISTS `active_petty_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_petty_view`  AS SELECT `active_petty`.`AccountNo` AS `AccountNo`, `ledger_account`.`AccountName` AS `AccountName` FROM (`active_petty` join `ledger_account` on(`active_petty`.`AccountNo` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `active_students`
--
DROP TABLE IF EXISTS `active_students`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_students`  AS SELECT `student_promo`.`StudentID` AS `StudentID`, `student_main_view`.`FullName` AS `FullName`, `sub_class_main_view`.`CourseID` AS `CourseID`, `sub_class_main_view`.`ClassName` AS `CourseName`, `student_promo`.`PromoClass` AS `CurrentClassID`, `sub_class_main_view`.`SubClassName` AS `CurrentClassName`, `student_main_view`.`STelNo` AS `STelNo`, `student_main_view`.`TelNo` AS `TelNo`, `student_promo`.`Date` AS `Date`, `student_promo`.`Time` AS `Time`, `student_promo`.`Username` AS `Username`, `student_promo`.`Status` AS `Status` FROM ((`student_promo` join `student_main_view` on(`student_promo`.`StudentID` = `student_main_view`.`StudentID`)) join `sub_class_main_view` on(`student_promo`.`PromoClass` = `sub_class_main_view`.`SubClassID`)) WHERE `student_promo`.`Status` = '1' ;

-- --------------------------------------------------------

--
-- Structure for view `active_students_fee`
--
DROP TABLE IF EXISTS `active_students_fee`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_students_fee`  AS SELECT `student_promo`.`StudentID` AS `StudentID`, `student_main_view`.`FullName` AS `FullName`, `sub_class_main_view`.`CourseID` AS `CourseID`, `sub_class_main_view`.`ClassName` AS `CourseName`, `student_promo`.`PromoClass` AS `CurrentClassID`, `sub_class_main_view`.`SubClassName` AS `CurrentClassName`, `student_main_view`.`STelNo` AS `STelNo`, `student_main_view`.`TelNo` AS `TelNo`, `student_fee_outstading_view_0`.`Balance` AS `Balance`, `student_promo`.`Date` AS `Date`, `student_promo`.`Time` AS `Time`, `student_promo`.`Username` AS `Username`, `student_promo`.`Status` AS `Status`, `student_main_view`.`BranchID` AS `BranchID` FROM (((`student_promo` join `student_main_view` on(`student_promo`.`StudentID` = `student_main_view`.`StudentID`)) join `sub_class_main_view` on(`student_promo`.`PromoClass` = `sub_class_main_view`.`SubClassID`)) left join `student_fee_outstading_view_0` on(`student_main_view`.`StudentID` = `student_fee_outstading_view_0`.`StudentID`)) WHERE `student_promo`.`Status` = '1' ;

-- --------------------------------------------------------

--
-- Structure for view `active_student_ticket_term_rpt`
--
DROP TABLE IF EXISTS `active_student_ticket_term_rpt`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_student_ticket_term_rpt`  AS SELECT `algor`.`TicketNo` AS `TicketNo`, `algor`.`SubClassID` AS `SubClassID`, `algor`.`StudentID` AS `StudentID`, `algor`.`CouponNo` AS `CouponNo`, `algor`.`Validation` AS `Validation`, `algor`.`Date` AS `Date`, `algor`.`Time` AS `Time`, `algor`.`Username` AS `Username`, `algor`.`Status` AS `Status` FROM `algor` WHERE `algor`.`Validation` > 0 AND `algor`.`Status` = 1 ;

-- --------------------------------------------------------

--
-- Structure for view `active_write_off_view`
--
DROP TABLE IF EXISTS `active_write_off_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_write_off_view`  AS SELECT `active_write_off`.`AccountNo` AS `AccountNo`, `ledger_account`.`AccountName` AS `AccountName`, `ledger_account`.`Type` AS `Type` FROM (`active_write_off` join `ledger_account` on(`active_write_off`.`AccountNo` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `algor_view`
--
DROP TABLE IF EXISTS `algor_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `algor_view`  AS SELECT `algor`.`TicketNo` AS `TicketNo`, `algor`.`SubClassID` AS `SubClassID`, `sub_class_main_view`.`SubClassName` AS `SubClassName`, `algor`.`StudentID` AS `StudentID`, `student_main_view`.`FullName` AS `FullName`, `algor`.`CouponNo` AS `CouponNo`, `algor`.`Validation` AS `Validation`, `algor`.`Date` AS `Date`, `algor`.`Time` AS `Time`, `algor`.`Username` AS `Username`, `algor`.`Status` AS `Status` FROM ((`algor` join `student_main_view` on(`algor`.`StudentID` = `student_main_view`.`StudentID`)) join `sub_class_main_view` on(`algor`.`SubClassID` = `sub_class_main_view`.`SubClassID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `bsheet_view`
--
DROP TABLE IF EXISTS `bsheet_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `bsheet_view`  AS SELECT `journal`.`AccountID` AS `AccountID`, `journal`.`SubAccountID` AS `SubAccountID`, `journal`.`Mode` AS `Mode`, `journal`.`TType` AS `TType`, `journal`.`ReceiptNo` AS `ReceiptNo`, `journal`.`Dr` AS `Dr`, `journal`.`Cr` AS `Cr`, `journal`.`Description` AS `Description`, `journal`.`Date` AS `Date`, `rpt_multi_values`.`FDate` AS `FDate`, `journal`.`Time` AS `Time`, `journal`.`Username` AS `Username`, `journal`.`BranchID` AS `BranchID` FROM (`journal` join `rpt_multi_values`) WHERE `journal`.`Date` <= `rpt_multi_values`.`FDate` AND `journal`.`BranchID` = `rpt_multi_values`.`SubjectID` ;

-- --------------------------------------------------------

--
-- Structure for view `bsheet_view_1`
--
DROP TABLE IF EXISTS `bsheet_view_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `bsheet_view_1`  AS SELECT `bsheet_view`.`AccountID` AS `AccountID`, round(sum(`bsheet_view`.`Dr`),2) AS `TDr`, round(sum(`bsheet_view`.`Cr`),2) AS `TCr`, `bsheet_view`.`Date` AS `Date`, `bsheet_view`.`FDate` AS `FDate`, `bsheet_view`.`BranchID` AS `BranchID` FROM `bsheet_view` GROUP BY `bsheet_view`.`AccountID` ;

-- --------------------------------------------------------

--
-- Structure for view `bsheet_view_2`
--
DROP TABLE IF EXISTS `bsheet_view_2`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `bsheet_view_2`  AS SELECT `ledger_account_view`.`ControlID` AS `ControlID`, `ledger_account_view`.`ControlName` AS `ControlName`, `ledger_account_view`.`CategoryID` AS `CategoryID`, `ledger_account_view`.`Class` AS `Class`, `bsheet_view_1`.`AccountID` AS `AccountID`, `ledger_account_view`.`AccountName` AS `AccountName`, `bsheet_view_1`.`TDr` AS `TDr`, `bsheet_view_1`.`TCr` AS `TCr`, round(`bsheet_view_1`.`TCr` - `bsheet_view_1`.`TDr`,2) AS `Diff`, `bsheet_view_1`.`Date` AS `Date`, `bsheet_view_1`.`FDate` AS `FDate`, `bsheet_view_1`.`BranchID` AS `BranchID` FROM (`bsheet_view_1` left join `ledger_account_view` on(`bsheet_view_1`.`AccountID` = `ledger_account_view`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `bsheet_view_3`
--
DROP TABLE IF EXISTS `bsheet_view_3`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `bsheet_view_3`  AS SELECT `bsheet_view_2`.`ControlID` AS `ControlID`, `bsheet_view_2`.`ControlName` AS `ControlName`, `ledger_category`.`CategoryID` AS `CategoryID`, `ledger_category`.`CategoryName` AS `CategoryName`, `ledger_category`.`SubCategoryID` AS `SubCategoryID`, `ledger_category`.`SubCategoryName` AS `SubCategoryName`, `bsheet_view_2`.`Class` AS `Class`, `bsheet_view_2`.`AccountID` AS `AccountID`, `bsheet_view_2`.`AccountName` AS `AccountName`, `bsheet_view_2`.`TDr` AS `TDr`, `bsheet_view_2`.`TCr` AS `TCr`, `bsheet_view_2`.`Diff` AS `Diff`, `bsheet_view_2`.`Date` AS `Date`, `bsheet_view_2`.`FDate` AS `FDate`, `bsheet_view_2`.`BranchID` AS `BranchID` FROM (`bsheet_view_2` left join `ledger_category` on(`bsheet_view_2`.`CategoryID` = `ledger_category`.`SubCategoryID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `cargo_manifestation_breakdown`
--
DROP TABLE IF EXISTS `cargo_manifestation_breakdown`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `cargo_manifestation_breakdown`  AS SELECT `manifestation_breakdown`.`ConsignmentID` AS `ConsignmentID`, `manifestation_breakdown`.`MainBL` AS `MainBL`, `manifestation_breakdown`.`HouseBL` AS `HouseBL`, `manifestation_breakdown`.`ConsigneeID` AS `ConsigneeID`, `manifestation_breakdown`.`Consigenee2_ID` AS `Consigenee2_ID`, `manifestation_breakdown`.`Description` AS `Description`, `manifestation_breakdown`.`Weight` AS `Weight`, `manifestation_breakdown`.`Package` AS `Package`, `manifestation_breakdown`.`Unit` AS `Unit`, `manifestation_breakdown`.`Username` AS `Username`, `manifestation_breakdown`.`Date` AS `Date`, `manifestation_breakdown`.`Time` AS `Time`, `manifestation_breakdown`.`Status` AS `Status` FROM `manifestation_breakdown` WHERE 1 ;

-- --------------------------------------------------------

--
-- Structure for view `class_main_view`
--
DROP TABLE IF EXISTS `class_main_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `class_main_view`  AS SELECT `class_main`.`CategoryID` AS `CategoryID`, `class_main`.`ClassID` AS `ClassID`, `class_main`.`ClassName` AS `ClassName`, `class_main`.`Username` AS `Username`, `class_main`.`Date` AS `Date`, `class_main`.`Time` AS `Time`, `class_main`.`Status` AS `Status` FROM `class_main` ;

-- --------------------------------------------------------

--
-- Structure for view `class_population`
--
DROP TABLE IF EXISTS `class_population`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `class_population`  AS SELECT count(distinct `student_test_score`.`StudentID`) AS `Poopulation`, `student_test_score`.`SubClassID` AS `CurrentClassID`, `student_test_score`.`CouponNo` AS `CouponNo` FROM `student_test_score` GROUP BY `student_test_score`.`SubClassID`, `student_test_score`.`CouponNo` ;

-- --------------------------------------------------------

--
-- Structure for view `class_subject_view`
--
DROP TABLE IF EXISTS `class_subject_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `class_subject_view`  AS SELECT `class_subject`.`ClassID` AS `CourseID`, `class_main`.`ClassName` AS `CourseName`, `class_subject`.`SubjCategoryID` AS `SubjCategoryID`, `subject_main_view`.`CategoryName` AS `SubjCategoryName`, `class_subject`.`SubjectID` AS `SubjectID`, `subject_main_view`.`SubjectName` AS `SubjectName`, `class_subject`.`Date` AS `Date`, `class_subject`.`Time` AS `Time`, `class_subject`.`Username` AS `Username`, `class_subject`.`Status` AS `Status` FROM ((`class_subject` join `class_main` on(`class_subject`.`ClassID` = `class_main`.`ClassID`)) join `subject_main_view` on(`class_subject`.`SubjectID` = `subject_main_view`.`SubjectID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `consignment_profile_view`
--
DROP TABLE IF EXISTS `consignment_profile_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `consignment_profile_view`  AS SELECT `container_main`.`ConsignmentID` AS `ConsignmentID`, `container_main`.`ETA` AS `ETA`, `container_main`.`BL` AS `BL`, `container_main`.`ContainerNo` AS `ContainerNo`, `container_main`.`ContainerSize` AS `ContainerSize`, `container_main`.`ShipperID` AS `ShipperID`, `container_main`.`ContWeight` AS `ContWeight`, `container_main`.`Destination` AS `Destination`, `container_main`.`BranchID` AS `BranchID`, `container_main`.`Date` AS `Date`, `container_main`.`Time` AS `Time`, `container_main`.`Status` AS `Status`, `commodity_type`.`CategoryID` AS `CommodityCategoryID`, `container_main`.`CmdtTypeID` AS `CmdtTypeID`, `commodity_type`.`TypeName` AS `CommodityType`, `container_main`.`ConsigneeID` AS `ConsigneeID`, `consignee_main`.`FullName` AS `ConsigneeName`, `container_main`.`ReleaseType` AS `ReleaseTypeID`, `container_release`.`ReleaseType` AS `ReleaseType` FROM (((`container_main` left join `commodity_type` on(`container_main`.`CmdtTypeID` = `commodity_type`.`TypeID`)) join `container_release` on(`container_main`.`ReleaseType` = `container_release`.`ID`)) left join `consignee_main` on(`container_main`.`ConsigneeID` = `consignee_main`.`ConsigneeID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `consignment_profile_view_1`
--
DROP TABLE IF EXISTS `consignment_profile_view_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `consignment_profile_view_1`  AS SELECT `consignment_profile_view`.`ConsignmentID` AS `ConsignmentID`, `consignment_profile_view`.`ETA` AS `ETA`, `consignment_profile_view`.`BL` AS `BL`, `consignment_profile_view`.`ContainerNo` AS `ContainerNo`, `consignment_profile_view`.`ContainerSize` AS `ContainerSize`, `consignment_profile_view`.`ShipperID` AS `ShipperID`, `ship_carrier`.`CarrierName` AS `ShipperName`, `consignment_profile_view`.`ContWeight` AS `ContWeight`, `consignment_profile_view`.`Destination` AS `Destination`, `consignment_profile_view`.`BranchID` AS `BranchID`, `consignment_profile_view`.`Date` AS `Date`, `consignment_profile_view`.`Time` AS `Time`, `consignment_profile_view`.`Status` AS `Status`, `consignment_profile_view`.`CommodityCategoryID` AS `CommodityCategoryID`, `commodity_category`.`CategoryName` AS `CommodityCategory`, `consignment_profile_view`.`CmdtTypeID` AS `CmdtTypeID`, `consignment_profile_view`.`CommodityType` AS `CommodityType`, `consignment_profile_view`.`ConsigneeID` AS `ConsigneeID`, `consignment_profile_view`.`ConsigneeName` AS `ConsigneeName`, `consignment_profile_view`.`ReleaseTypeID` AS `ReleaseTypeID`, `consignment_profile_view`.`ReleaseType` AS `ReleaseType` FROM ((`consignment_profile_view` left join `commodity_category` on(`consignment_profile_view`.`CommodityCategoryID` = `commodity_category`.`ID`)) left join `ship_carrier` on(`consignment_profile_view`.`ShipperID` = `ship_carrier`.`CarrierID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `consignment_weight_temp_view`
--
DROP TABLE IF EXISTS `consignment_weight_temp_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `consignment_weight_temp_view`  AS SELECT `consignment_weight_temp`.`MainBL` AS `MainBL`, `consignment_weight_temp`.`ConsignmentID` AS `ConsignmentID`, round(sum(`consignment_weight_temp`.`Weight`),2) AS `Total`, `consignment_weight_temp`.`Username` AS `Username` FROM `consignment_weight_temp` GROUP BY `consignment_weight_temp`.`MainBL`, `consignment_weight_temp`.`ConsignmentID`, `consignment_weight_temp`.`Username` ;

-- --------------------------------------------------------

--
-- Structure for view `container_exp_pmt`
--
DROP TABLE IF EXISTS `container_exp_pmt`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `container_exp_pmt`  AS SELECT `hbl_invoice`.`ConsignmentID` AS `ConsignmentID`, `hbl_invoice`.`MainBL` AS `MainBL`, `hbl_invoice`.`HouseBL` AS `HouseBL`, `hbl_invoice`.`ConsigneeID` AS `ConsigneeID`, `hbl_invoice`.`ReceiptNo` AS `ReceiptNo`, `hbl_invoice`.`AccountNo` AS `AccountNo`, `hbl_invoice`.`Fee` AS `Fee`, `hbl_invoice`.`GetFundNHIL` AS `GetFundNHIL`, `hbl_invoice`.`VAT` AS `VAT`, `hbl_invoice`.`Date` AS `Date`, `hbl_invoice`.`Time` AS `Time`, `hbl_invoice`.`Username` AS `Username`, `hbl_invoice`.`Status` AS `Status` FROM `hbl_invoice` WHERE `hbl_invoice`.`Status` = 1 ;

-- --------------------------------------------------------

--
-- Structure for view `container_exp_pmt_0`
--
DROP TABLE IF EXISTS `container_exp_pmt_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `container_exp_pmt_0`  AS SELECT `container_exp_pmt`.`ConsignmentID` AS `ConsignmentID`, `container_exp_pmt`.`MainBL` AS `MainBL`, count(`container_exp_pmt`.`HouseBL`) AS `ConsCount`, round(sum(`container_exp_pmt`.`Fee`),2) AS `TFee`, `container_exp_pmt`.`GetFundNHIL` AS `GetFundNHIL`, `container_exp_pmt`.`VAT` AS `VAT`, `container_exp_pmt`.`Date` AS `Date`, `container_exp_pmt`.`Status` AS `Status` FROM `container_exp_pmt` GROUP BY `container_exp_pmt`.`ConsignmentID`, `container_exp_pmt`.`MainBL` ;

-- --------------------------------------------------------

--
-- Structure for view `container_exp_pmt_1`
--
DROP TABLE IF EXISTS `container_exp_pmt_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `container_exp_pmt_1`  AS SELECT `container_exp_pmt_0`.`ConsignmentID` AS `ConsignmentID`, `container_main_view`.`ShipperName` AS `ShipperName`, `container_main_view`.`VesselName` AS `VesselName`, `container_main_view`.`SealNo` AS `SealNo`, `container_main_view`.`ETA` AS `ETA`, `container_exp_pmt_0`.`MainBL` AS `MainBL`, `container_exp_pmt_0`.`ConsCount` AS `ConsCount`, `container_exp_pmt_0`.`TFee` AS `TFee`, CASE WHEN `container_exp_pmt_view_0`.`TDr` is null THEN 0 ELSE `container_exp_pmt_view_0`.`TDr` END AS `TDr`, `container_exp_pmt_0`.`GetFundNHIL` AS `GetFundNHIL`, round(`container_exp_pmt_0`.`TFee` * `container_exp_pmt_0`.`GetFundNHIL`,2) AS `GTFVal`, `container_exp_pmt_0`.`VAT` AS `VAT`, round((`container_exp_pmt_0`.`TFee` * `container_exp_pmt_0`.`GetFundNHIL` + `container_exp_pmt_0`.`TFee`) * `container_exp_pmt_0`.`VAT`,2) AS `VATVal`, `container_exp_pmt_0`.`Date` AS `Date`, `container_main_view`.`Date` AS `TDate`, `container_exp_pmt_0`.`Status` AS `Status` FROM ((`container_exp_pmt_0` join `container_main_view` on(`container_exp_pmt_0`.`ConsignmentID` = `container_main_view`.`ConsignmentID`)) left join `container_exp_pmt_view_0` on(`container_exp_pmt_0`.`MainBL` = `container_exp_pmt_view_0`.`MainBL`)) ;

-- --------------------------------------------------------

--
-- Structure for view `container_exp_pmt_2`
--
DROP TABLE IF EXISTS `container_exp_pmt_2`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `container_exp_pmt_2`  AS SELECT `container_exp_pmt_1`.`ConsignmentID` AS `ConsignmentID`, `container_exp_pmt_1`.`ShipperName` AS `ShipperName`, `container_exp_pmt_1`.`VesselName` AS `VesselName`, `container_exp_pmt_1`.`SealNo` AS `SealNo`, `container_exp_pmt_1`.`ETA` AS `ETA`, `container_exp_pmt_1`.`MainBL` AS `MainBL`, `container_exp_pmt_1`.`ConsCount` AS `ConsCount`, `container_exp_pmt_1`.`TFee` AS `TFee`, `container_exp_pmt_1`.`TDr` AS `TDr`, round(`container_exp_pmt_1`.`TFee` - `container_exp_pmt_1`.`TDr`,2) AS `Balance`, `container_exp_pmt_1`.`GetFundNHIL` AS `GetFundNHIL`, `container_exp_pmt_1`.`GTFVal` AS `GTFVal`, `container_exp_pmt_1`.`VAT` AS `VAT`, `container_exp_pmt_1`.`VATVal` AS `VATVal`, round(`container_exp_pmt_1`.`GTFVal` + `container_exp_pmt_1`.`VATVal`,2) AS `TotalTax`, `container_exp_pmt_1`.`Date` AS `Date`, `container_exp_pmt_1`.`TDate` AS `TDate`, `container_exp_pmt_1`.`Status` AS `Status` FROM `container_exp_pmt_1` ;

-- --------------------------------------------------------

--
-- Structure for view `container_exp_pmt_3`
--
DROP TABLE IF EXISTS `container_exp_pmt_3`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `container_exp_pmt_3`  AS SELECT `container_exp_pmt_2`.`ConsignmentID` AS `ConsignmentID`, `container_exp_pmt_2`.`ShipperName` AS `ShipperName`, `container_exp_pmt_2`.`VesselName` AS `VesselName`, `container_exp_pmt_2`.`SealNo` AS `SealNo`, `container_exp_pmt_2`.`ETA` AS `ETA`, `container_exp_pmt_2`.`MainBL` AS `MainBL`, `container_exp_pmt_2`.`ConsCount` AS `ConsCount`, `container_exp_pmt_2`.`TFee` AS `TFee`, `container_exp_pmt_2`.`TDr` AS `TDr`, `container_exp_pmt_2`.`Balance` AS `Balance`, `container_exp_pmt_2`.`GetFundNHIL` AS `GetFundNHIL`, `container_exp_pmt_2`.`GTFVal` AS `GTFVal`, `container_exp_pmt_2`.`VAT` AS `VAT`, `container_exp_pmt_2`.`VATVal` AS `VATVal`, `container_exp_pmt_2`.`TotalTax` AS `TotalTax`, `container_exp_pmt_2`.`Date` AS `Date`, `container_exp_pmt_2`.`TDate` AS `TDate`, `container_exp_pmt_2`.`Status` AS `Status` FROM `container_exp_pmt_2` WHERE `container_exp_pmt_2`.`Balance` > 0 ;

-- --------------------------------------------------------

--
-- Structure for view `container_exp_pmt_view`
--
DROP TABLE IF EXISTS `container_exp_pmt_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `container_exp_pmt_view`  AS SELECT `pnl_transaction`.`AccountID` AS `AccountID`, `pnl_transaction`.`Stamp` AS `Stamp`, `pnl_transaction`.`Mode` AS `Mode`, `pnl_transaction`.`MainBL` AS `MainBL`, `pnl_transaction`.`HouseBL` AS `HouseBL`, `pnl_transaction`.`ReceiptNo` AS `ReceiptNo`, `pnl_transaction`.`Description` AS `Description`, `pnl_transaction`.`Dr` AS `Dr`, `pnl_transaction`.`Cr` AS `Cr`, `pnl_transaction`.`Date` AS `Date`, `pnl_transaction`.`Time` AS `Time`, `pnl_transaction`.`BranchID` AS `BranchID`, `pnl_transaction`.`Username` AS `Username`, `pnl_transaction`.`Status` AS `Status` FROM `pnl_transaction` WHERE `pnl_transaction`.`Status` = '1' AND `pnl_transaction`.`Cr` = 0 ;

-- --------------------------------------------------------

--
-- Structure for view `container_exp_pmt_view_0`
--
DROP TABLE IF EXISTS `container_exp_pmt_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `container_exp_pmt_view_0`  AS SELECT `container_exp_pmt_view`.`AccountID` AS `AccountID`, `container_exp_pmt_view`.`Stamp` AS `Stamp`, `container_exp_pmt_view`.`Mode` AS `Mode`, `container_exp_pmt_view`.`MainBL` AS `MainBL`, `container_exp_pmt_view`.`HouseBL` AS `HouseBL`, `container_exp_pmt_view`.`ReceiptNo` AS `ReceiptNo`, `container_exp_pmt_view`.`Description` AS `Description`, round(sum(`container_exp_pmt_view`.`Dr`),2) AS `TDr`, `container_exp_pmt_view`.`Cr` AS `Cr`, `container_exp_pmt_view`.`Date` AS `Date`, `container_exp_pmt_view`.`Time` AS `Time`, `container_exp_pmt_view`.`BranchID` AS `BranchID`, `container_exp_pmt_view`.`Username` AS `Username`, `container_exp_pmt_view`.`Status` AS `Status` FROM `container_exp_pmt_view` GROUP BY `container_exp_pmt_view`.`MainBL` ;

-- --------------------------------------------------------

--
-- Structure for view `container_gate_out_view`
--
DROP TABLE IF EXISTS `container_gate_out_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `container_gate_out_view`  AS SELECT `container_details`.`ConsignmentID` AS `ConsignmentID`, `container_main`.`ConsigneeID` AS `ConsigneeID`, `container_details`.`BL` AS `BL`, `container_details`.`SealNo` AS `SealNo`, `container_details`.`ContainerNo` AS `ContainerNo`, `container_details`.`ContainerSize` AS `ContainerSize`, `container_details`.`Weight` AS `Weight`, `container_details`.`HandlingCost` AS `HandlingCost`, `container_details`.`Status` AS `Status`, `container_details`.`GateOutDate` AS `GateOutDate`, `container_details`.`ReturnDate` AS `ReturnedDate`, curdate() AS `TodayDate`, CASE WHEN `container_details`.`ReturnDate` is null OR `container_details`.`ReturnDate` = '0000-00-00' THEN to_days(curdate()) - to_days(`container_details`.`GateOutDate`) ELSE to_days(`container_details`.`ReturnDate`) - to_days(`container_details`.`GateOutDate`) END AS `Demurrage`, `container_details`.`Username` AS `Username`, `container_details`.`BranchID` AS `BranchID`, `container_details`.`Date` AS `Date`, `container_details`.`Time` AS `Time` FROM (`container_details` join `container_main` on(`container_details`.`BL` = `container_main`.`BL` and `container_main`.`ConsignmentID` = `container_details`.`ConsignmentID`)) WHERE `container_details`.`Status` = 3 ;

-- --------------------------------------------------------

--
-- Structure for view `container_main_view`
--
DROP TABLE IF EXISTS `container_main_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `container_main_view`  AS SELECT `container_main`.`ConsignmentID` AS `ConsignmentID`, `container_main`.`ShipperID` AS `ShipperID`, `shipper_main`.`ShipperName` AS `ShipperName`, `container_main`.`VesselName` AS `VesselName`, `container_main`.`VoyageNo` AS `VoyageNo`, `container_main`.`SealNo` AS `SealNo`, `container_main`.`ETA` AS `ETA`, `container_main`.`BL` AS `BL`, `container_details`.`ContainerNo` AS `ContainerNo`, `container_main`.`SOB` AS `SOB`, `container_main`.`ContainerSize` AS `ContainerSize`, `container_main`.`POL_ID` AS `POL_ID`, `pol`.`POL_Name` AS `POL_Name`, `container_main`.`POD_ID` AS `POD_ID`, `pod`.`POD_Name` AS `POD_Name`, round(`container_details`.`Weight`,2) AS `ContWeight`, `container_main`.`AgentContact` AS `AgentContact`, `container_main`.`Username` AS `Username`, `container_main`.`Date` AS `Date`, `container_main`.`Time` AS `Time`, `container_main`.`Status` AS `Status` FROM ((((`container_main` join `pol` on(`container_main`.`POL_ID` = `pol`.`POL_ID`)) join `pod` on(`container_main`.`POD_ID` = `pod`.`POD_ID`)) join `shipper_main` on(`container_main`.`ShipperID` = `shipper_main`.`ShipperID`)) left join `container_details` on(`container_main`.`ConsignmentID` = `container_details`.`ConsignmentID` and `container_main`.`BL` = `container_details`.`BL`)) ;

-- --------------------------------------------------------

--
-- Structure for view `container_main_view_0`
--
DROP TABLE IF EXISTS `container_main_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `container_main_view_0`  AS SELECT `container_main_view`.`ConsignmentID` AS `ConsignmentID`, `container_main_view`.`ShipperID` AS `ShipperID`, `container_main_view`.`ShipperName` AS `ShipperName`, `container_main_view`.`VesselName` AS `VesselName`, `container_main_view`.`ETA` AS `ETA`, `container_main_view`.`BL` AS `BL`, `container_main_view`.`ContainerNo` AS `ContainerNo`, `container_main_view`.`ContainerSize` AS `ContainerSize`, `container_main_view`.`POL_ID` AS `POL_ID`, `container_main_view`.`POL_Name` AS `POL_Name`, `container_main_view`.`POD_ID` AS `POD_ID`, `container_main_view`.`POD_Name` AS `POD_Name`, `container_main_view`.`ContWeight` AS `ContWeight`, CASE WHEN `manifestation_breakdown_search`.`TWeight` is not null THEN `manifestation_breakdown_search`.`TWeight` ELSE 0 END AS `TWeight`, `container_main_view`.`Username` AS `Username`, `container_main_view`.`Date` AS `Date`, `container_main_view`.`Time` AS `Time`, `container_main_view`.`Status` AS `Status` FROM (`container_main_view` left join `manifestation_breakdown_search` on(`container_main_view`.`ConsignmentID` = `manifestation_breakdown_search`.`ConsignmentID` and `container_main_view`.`BL` = `manifestation_breakdown_search`.`MainBL`)) ;

-- --------------------------------------------------------

--
-- Structure for view `container_main_view_1`
--
DROP TABLE IF EXISTS `container_main_view_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `container_main_view_1`  AS SELECT `container_main_view_0`.`ConsignmentID` AS `ConsignmentID`, `container_main_view_0`.`ShipperID` AS `ShipperID`, `container_main_view_0`.`ShipperName` AS `ShipperName`, `container_main_view_0`.`VesselName` AS `VesselName`, `container_main_view_0`.`ETA` AS `ETA`, to_days(`container_main_view_0`.`ETA`) - to_days(curdate()) AS `ETA_Days`, `container_main_view_0`.`BL` AS `BL`, `container_main_view_0`.`ContainerNo` AS `ContainerNo`, `container_main_view_0`.`ContainerSize` AS `ContainerSize`, `container_main_view_0`.`POL_ID` AS `POL_ID`, `container_main_view_0`.`POL_Name` AS `POL_Name`, `container_main_view_0`.`POD_ID` AS `POD_ID`, `container_main_view_0`.`POD_Name` AS `POD_Name`, `container_main_view_0`.`ContWeight` AS `ContWeight`, `container_main_view_0`.`TWeight` AS `TWeight`, `container_main_view_0`.`Username` AS `Username`, `container_main_view_0`.`Date` AS `Date`, `container_main_view_0`.`Time` AS `Time`, `container_main_view_0`.`Status` AS `Status` FROM `container_main_view_0` WHERE `container_main_view_0`.`ContWeight` > `container_main_view_0`.`TWeight` ;

-- --------------------------------------------------------

--
-- Structure for view `container_main_view_2`
--
DROP TABLE IF EXISTS `container_main_view_2`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `container_main_view_2`  AS SELECT `container_main_view_1`.`ConsignmentID` AS `ConsignmentID`, `container_main_view_1`.`ShipperID` AS `ShipperID`, `container_main_view_1`.`ShipperName` AS `ShipperName`, `container_main_view_1`.`VesselName` AS `VesselName`, `container_main_view_1`.`ETA` AS `ETA`, `container_main_view_1`.`BL` AS `BL`, `container_main_view_1`.`ContainerNo` AS `ContainerNo`, `container_main_view_1`.`ContainerSize` AS `ContainerSize`, `container_main_view_1`.`POL_ID` AS `POL_ID`, `container_main_view_1`.`POL_Name` AS `POL_Name`, `container_main_view_1`.`POD_ID` AS `POD_ID`, `container_main_view_1`.`POD_Name` AS `POD_Name`, `container_main_view_1`.`ContWeight` AS `ContWeight`, CASE WHEN `temp_manifestation_breakdown_view_0`.`TWeight` is null THEN 0 ELSE `temp_manifestation_breakdown_view_0`.`TWeight` END AS `TempWeight`, `container_main_view_1`.`TWeight` AS `TWeight`, `container_main_view_1`.`Username` AS `Username`, `container_main_view_1`.`Date` AS `Date`, `container_main_view_1`.`Time` AS `Time`, `container_main_view_1`.`Status` AS `Status` FROM (`container_main_view_1` left join `temp_manifestation_breakdown_view_0` on(`container_main_view_1`.`ConsignmentID` = `temp_manifestation_breakdown_view_0`.`ConsignmentID` and `container_main_view_1`.`BL` = `temp_manifestation_breakdown_view_0`.`MainBL` and `container_main_view_1`.`ContainerNo` = `temp_manifestation_breakdown_view_0`.`ContainerNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `container_main_view_3`
--
DROP TABLE IF EXISTS `container_main_view_3`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `container_main_view_3`  AS SELECT `container_main_view_2`.`ConsignmentID` AS `ConsignmentID`, `container_main_view_2`.`ShipperID` AS `ShipperID`, `container_main_view_2`.`ShipperName` AS `ShipperName`, `container_main_view_2`.`VesselName` AS `VesselName`, `container_main_view_2`.`ETA` AS `ETA`, `container_main_view_2`.`BL` AS `BL`, `container_main_view_2`.`ContainerNo` AS `ContainerNo`, `container_main_view_2`.`ContainerSize` AS `ContainerSize`, `container_main_view_2`.`POL_ID` AS `POL_ID`, `container_main_view_2`.`POL_Name` AS `POL_Name`, `container_main_view_2`.`POD_ID` AS `POD_ID`, `container_main_view_2`.`POD_Name` AS `POD_Name`, `container_main_view_2`.`ContWeight` AS `ContWeight`, `container_main_view_2`.`TempWeight` AS `TempWeight`, `container_main_view_2`.`TWeight` AS `TWeight`, `container_main_view_2`.`Username` AS `Username`, `container_main_view_2`.`Date` AS `Date`, `container_main_view_2`.`Time` AS `Time`, `container_main_view_2`.`Status` AS `Status` FROM `container_main_view_2` WHERE `container_main_view_2`.`ContWeight` > `container_main_view_2`.`TempWeight` ;

-- --------------------------------------------------------

--
-- Structure for view `container_main_view_total_weight`
--
DROP TABLE IF EXISTS `container_main_view_total_weight`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `container_main_view_total_weight`  AS SELECT `container_main_view_1`.`ConsignmentID` AS `ConsignmentID`, `container_main_view_1`.`BL` AS `BL`, round(sum(`container_main_view_1`.`ContWeight`),2) AS `BLWeight`, `container_main_view_1`.`TWeight` AS `TWeight`, `container_main_view_1`.`Username` AS `Username`, `container_main_view_1`.`Date` AS `Date`, `container_main_view_1`.`Time` AS `Time`, `container_main_view_1`.`Status` AS `Status` FROM `container_main_view_1` GROUP BY `container_main_view_1`.`BL`, `container_main_view_1`.`Username` ;

-- --------------------------------------------------------

--
-- Structure for view `correction_container_size_view`
--
DROP TABLE IF EXISTS `correction_container_size_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `correction_container_size_view`  AS SELECT `container_main`.`Rotation` AS `Rotation`, `container_main`.`ShipperID` AS `ShipperID`, `container_main`.`VesselName` AS `VesselName`, `container_main`.`VoyageNo` AS `VoyageNo`, `container_main`.`SealNo` AS `SealNo`, `container_details`.`SealNo` AS `SealNo1`, `container_main`.`ETA` AS `ETA`, `container_main`.`BL` AS `BL`, `container_main`.`ContainerNo` AS `ContainerNo`, `container_details`.`ContainerNo` AS `ContainerNo1`, `container_main`.`ContainerSize` AS `ContainerSize`, `container_details`.`ContainerSize` AS `ContainerSize1`, `container_main`.`ReceiptNo` AS `ReceiptNo`, `container_main`.`ContWeight` AS `ContWeight`, `container_main`.`Charges` AS `Charges`, `container_details`.`HandlingCost` AS `HandlingCost`, `container_main`.`AgentContact` AS `AgentContact` FROM (`container_main` join `container_details` on(`container_main`.`BL` = `container_details`.`BL`)) WHERE `container_main`.`ContainerSize` like `container_main`.`Charges` <> `container_details`.`HandlingCost` ;

-- --------------------------------------------------------

--
-- Structure for view `ctrl_fee_receivable_view`
--
DROP TABLE IF EXISTS `ctrl_fee_receivable_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ctrl_fee_receivable_view`  AS SELECT `ctrl_fee_receivable`.`AccountID` AS `AccountID`, `ledger_account`.`AccountName` AS `AccountName` FROM (`ctrl_fee_receivable` join `ledger_account` on(`ctrl_fee_receivable`.`AccountID` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `ctrl_student_view`
--
DROP TABLE IF EXISTS `ctrl_student_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ctrl_student_view`  AS SELECT `ctrl_student`.`AccountID` AS `AccountID`, `ledger_account`.`AccountName` AS `AccountName` FROM (`ctrl_student` join `ledger_account` on(`ctrl_student`.`AccountID` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `declaration_main_view`
--
DROP TABLE IF EXISTS `declaration_main_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `declaration_main_view`  AS SELECT `declaration_main`.`DeclarationID` AS `DeclarationID`, `declaration_main`.`BL` AS `BL`, `declaration_main`.`DeclarationNo` AS `DeclarationNo`, `declaration_main`.`ItemDescription` AS `ItemDescription`, `declaration_main`.`DutyPaid` AS `DutyPaid`, `declaration_main`.`Amount` AS `Amount`, `declaration_main`.`AgentName` AS `AgentName`, `declaration_main`.`AgentContact` AS `AgentContact`, `declaration_main`.`ContainerSize` AS `ContainerSize`, `declaration_main`.`ReceiptNo` AS `ReceiptNo`, `declaration_main`.`Date` AS `Date`, `declaration_main`.`Time` AS `Time`, `declaration_main`.`Username` AS `Username`, `kaina`.`FullName` AS `FullName`, `declaration_main`.`BranchID` AS `BranchID`, `declaration_main`.`Status` AS `Status` FROM (`declaration_main` left join `kaina` on(`declaration_main`.`Username` = `kaina`.`ID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `declaration_main_view_0`
--
DROP TABLE IF EXISTS `declaration_main_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `declaration_main_view_0`  AS SELECT `declaration_main_view`.`DeclarationID` AS `DeclarationID`, `manifestation_breakdown_view`.`MainBL` AS `MainBL`, `declaration_main_view`.`BL` AS `HBL`, `manifestation_breakdown_view`.`ConsigneeID` AS `ConsigneeID`, `manifestation_breakdown_view`.`FullName` AS `ConsigneeName`, `declaration_main_view`.`DeclarationNo` AS `DeclarationNo`, `declaration_main_view`.`ItemDescription` AS `ItemDescription`, `declaration_main_view`.`DutyPaid` AS `DutyPaid`, `declaration_main_view`.`Amount` AS `Amount`, `declaration_main_view`.`AgentName` AS `AgentName`, `declaration_main_view`.`AgentContact` AS `AgentContact`, `declaration_main_view`.`ContainerSize` AS `ContainerSize`, `declaration_main_view`.`ReceiptNo` AS `ReceiptNo`, `declaration_main_view`.`Date` AS `Date`, `declaration_main_view`.`Time` AS `Time`, `declaration_main_view`.`Username` AS `Username`, `declaration_main_view`.`FullName` AS `FullName`, `declaration_main_view`.`BranchID` AS `BranchID`, `declaration_main_view`.`Status` AS `Status` FROM (`declaration_main_view` left join `manifestation_breakdown_view` on(`declaration_main_view`.`BL` = `manifestation_breakdown_view`.`HouseBL`)) ;

-- --------------------------------------------------------

--
-- Structure for view `declaration_process_search`
--
DROP TABLE IF EXISTS `declaration_process_search`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `declaration_process_search`  AS SELECT `manifestation_breakdown`.`ConsignmentID` AS `ConsignmentID`, `manifestation_breakdown`.`MainBL` AS `MainBL`, `manifestation_breakdown`.`HouseBL` AS `HouseBL`, `container_main`.`ContainerSize` AS `ContainerSize`, `manifestation_breakdown`.`Weight` AS `Weight`, `manifestation_breakdown`.`Description` AS `Description`, `manifestation_breakdown`.`OtherInfo` AS `OtherInfo`, `container_main`.`AgentContact` AS `AgentContact` FROM (`manifestation_breakdown` left join `container_main` on(`manifestation_breakdown`.`ConsignmentID` = `container_main`.`ConsignmentID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `disbursement_accounts_view`
--
DROP TABLE IF EXISTS `disbursement_accounts_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `disbursement_accounts_view`  AS SELECT `disbursement_accounts`.`AccountNo` AS `AccountNo`, `ledger_account`.`AccountName` AS `AccountName`, `disbursement_accounts`.`Username` AS `Username`, `disbursement_accounts`.`Date` AS `Date` FROM (`disbursement_accounts` join `ledger_account` on(`disbursement_accounts`.`AccountNo` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `disbursement_analysis_chart`
--
DROP TABLE IF EXISTS `disbursement_analysis_chart`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `disbursement_analysis_chart`  AS SELECT `disbursement_analysis`.`ConsigneeID` AS `ConsigneeID`, `disbursement_analysis`.`BL` AS `BL`, `container_main`.`ETA` AS `ETA`, `disbursement_analysis`.`HBL` AS `HBL`, `disbursement_analysis`.`ContainerNo` AS `ContainerNo`, `disbursement_analysis`.`TotalCashReceipt` AS `TotalCashReceipt`, `disbursement_analysis`.`ReceiptNo` AS `ReceiptNo`, `disbursement_analysis`.`AccountID` AS `AccountID`, `disbursement_analysis`.`Revenue` AS `Revenue`, `disbursement_analysis`.`Expenditure` AS `Expenditure`, `disbursement_analysis`.`Username` AS `Username`, `disbursement_analysis`.`Date` AS `Date`, `disbursement_analysis`.`Time` AS `Time`, `disbursement_analysis`.`Status` AS `Status`, `disbursement_analysis`.`Type` AS `Type` FROM (`disbursement_analysis` join `container_main` on(`disbursement_analysis`.`BL` = `container_main`.`BL`)) ;

-- --------------------------------------------------------

--
-- Structure for view `disbursement_analysis_chart_0`
--
DROP TABLE IF EXISTS `disbursement_analysis_chart_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `disbursement_analysis_chart_0`  AS SELECT `disbursement_analysis_chart`.`ConsigneeID` AS `ConsigneeID`, `disbursement_analysis_chart`.`BL` AS `BL`, `disbursement_analysis_chart`.`HBL` AS `HBL`, `disbursement_analysis_chart`.`ContainerNo` AS `ContainerNo`, `disbursement_analysis_chart`.`TotalCashReceipt` AS `TotalCashReceipt`, `disbursement_analysis_chart`.`ReceiptNo` AS `ReceiptNo`, `disbursement_analysis_chart`.`AccountID` AS `AccountID`, `ledger_account`.`AccountName` AS `AccountName`, `disbursement_analysis_chart`.`Revenue` AS `Revenue`, `disbursement_analysis_chart`.`Expenditure` AS `Expenditure`, `disbursement_analysis_chart`.`Username` AS `Username`, `disbursement_analysis_chart`.`Date` AS `Date`, `disbursement_analysis_chart`.`Time` AS `Time`, `disbursement_analysis_chart`.`Status` AS `Status`, `disbursement_analysis_chart`.`Type` AS `Type` FROM (`disbursement_analysis_chart` join `ledger_account` on(`disbursement_analysis_chart`.`AccountID` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `disbursement_analysis_distinct_hbl`
--
DROP TABLE IF EXISTS `disbursement_analysis_distinct_hbl`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `disbursement_analysis_distinct_hbl`  AS SELECT `disbursement_analysis_view`.`ContainerNo` AS `ContainerNo`, `disbursement_analysis_view`.`BL` AS `BL`, `disbursement_analysis_view`.`HBL` AS `HBL`, `disbursement_analysis_view`.`TotalCashReceipt` AS `TotalCashReceipt`, `disbursement_analysis_view`.`ReceiptNo` AS `ReceiptNo`, round(sum(`disbursement_analysis_view`.`Expenditure`),2) AS `TotalExpenditure`, `disbursement_analysis_view`.`Date` AS `Date`, `disbursement_analysis_view`.`Status` AS `Status`, `disbursement_analysis_view`.`Type` AS `Type`, `disbursement_analysis_view`.`Time` AS `Time` FROM `disbursement_analysis_view` GROUP BY `disbursement_analysis_view`.`HBL` ;

-- --------------------------------------------------------

--
-- Structure for view `disbursement_analysis_distinct_hbl_0`
--
DROP TABLE IF EXISTS `disbursement_analysis_distinct_hbl_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `disbursement_analysis_distinct_hbl_0`  AS SELECT `disbursement_analysis_distinct_hbl`.`ContainerNo` AS `ContainerNo`, `disbursement_analysis_distinct_hbl`.`TotalCashReceipt` AS `TotalCashReceipt`, count(`disbursement_analysis_distinct_hbl`.`HBL`) AS `BL_COUNT`, round(sum(`disbursement_analysis_distinct_hbl`.`TotalExpenditure`),2) AS `TExpenditure`, `disbursement_analysis_distinct_hbl`.`Date` AS `Date`, `disbursement_analysis_distinct_hbl`.`Status` AS `Status`, `disbursement_analysis_distinct_hbl`.`Type` AS `Type`, `disbursement_analysis_distinct_hbl`.`Time` AS `Time` FROM `disbursement_analysis_distinct_hbl` GROUP BY `disbursement_analysis_distinct_hbl`.`BL` ORDER BY `disbursement_analysis_distinct_hbl`.`Date` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `disbursement_analysis_unauth`
--
DROP TABLE IF EXISTS `disbursement_analysis_unauth`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `disbursement_analysis_unauth`  AS SELECT `disbursement_analysis`.`ConsigneeID` AS `ConsigneeID`, `disbursement_analysis`.`BL` AS `BL`, `disbursement_analysis`.`HBL` AS `HBL`, `disbursement_analysis`.`ContainerNo` AS `ContainerNo`, `disbursement_analysis`.`TotalCashReceipt` AS `TotalCashReceipt`, `disbursement_analysis`.`ReceiptNo` AS `ReceiptNo`, `disbursement_analysis`.`AccountID` AS `AccountID`, `disbursement_analysis`.`Expenditure` AS `Expenditure`, `disbursement_analysis`.`Username` AS `Username`, `disbursement_analysis`.`Date` AS `Date`, `disbursement_analysis`.`Time` AS `Time`, `disbursement_analysis`.`Status` AS `Status`, `disbursement_analysis`.`Type` AS `Type` FROM `disbursement_analysis` WHERE `disbursement_analysis`.`Status` <> 0 ;

-- --------------------------------------------------------

--
-- Structure for view `disbursement_analysis_unauth_0`
--
DROP TABLE IF EXISTS `disbursement_analysis_unauth_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `disbursement_analysis_unauth_0`  AS SELECT DISTINCT `disbursement_analysis_unauth`.`ContainerNo` AS `HBL` FROM `disbursement_analysis_unauth` WHERE `disbursement_analysis_unauth`.`Status` = 2 ;

-- --------------------------------------------------------

--
-- Structure for view `disbursement_analysis_unauth_1`
--
DROP TABLE IF EXISTS `disbursement_analysis_unauth_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `disbursement_analysis_unauth_1`  AS SELECT `disbursement_analysis_unauth`.`ConsigneeID` AS `ConsigneeID`, `disbursement_analysis_unauth`.`BL` AS `BL`, `disbursement_analysis_unauth`.`HBL` AS `HBL`, `disbursement_analysis_unauth`.`ContainerNo` AS `ContainerNo`, `disbursement_analysis_unauth`.`TotalCashReceipt` AS `TotalCashReceipt`, `disbursement_analysis_unauth`.`ReceiptNo` AS `ReceiptNo`, `disbursement_analysis_unauth`.`AccountID` AS `AccountID`, `ledger_account_expenditure`.`AccountName` AS `AccountName`, `disbursement_analysis_unauth`.`Expenditure` AS `Expenditure`, `disbursement_analysis_unauth`.`Username` AS `Username`, `disbursement_analysis_unauth`.`Date` AS `Date`, `disbursement_analysis_unauth`.`Time` AS `Time`, `disbursement_analysis_unauth`.`Status` AS `Status`, `disbursement_analysis_unauth`.`Type` AS `Type` FROM (`disbursement_analysis_unauth` left join `ledger_account_expenditure` on(`disbursement_analysis_unauth`.`AccountID` = `ledger_account_expenditure`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `disbursement_analysis_unauth_2`
--
DROP TABLE IF EXISTS `disbursement_analysis_unauth_2`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `disbursement_analysis_unauth_2`  AS SELECT `disbursement_analysis_unauth_1`.`ConsigneeID` AS `ConsigneeID`, `disbursement_analysis_unauth_1`.`ConsigneeName` AS `ConsigneeName`, `disbursement_analysis_unauth_1`.`BL` AS `BL`, `disbursement_analysis_unauth_1`.`HBL` AS `HBL`, `disbursement_analysis_unauth_1`.`ContainerNo` AS `ContainerNo`, `disbursement_analysis_unauth_1`.`Description` AS `Description`, `disbursement_analysis_unauth_1`.`TotalCashReceipt` AS `TotalCashReceipt`, `disbursement_analysis_unauth_1`.`ReceiptNo` AS `ReceiptNo`, `disbursement_analysis_unauth_1`.`AccountID` AS `AccountID`, `disbursement_analysis_unauth_1`.`Expenditure` AS `Expenditure`, `disbursement_analysis_unauth_1`.`Username` AS `Username`, `disbursement_analysis_unauth_1`.`Date` AS `Date`, `disbursement_analysis_unauth_1`.`Time` AS `Time`, `disbursement_analysis_unauth_1`.`Status` AS `Status`, `disbursement_analysis_unauth_1`.`Type` AS `Type` FROM `disbursement_analysis_unauth_1` ;

-- --------------------------------------------------------

--
-- Structure for view `disbursement_analysis_unauth_3`
--
DROP TABLE IF EXISTS `disbursement_analysis_unauth_3`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `disbursement_analysis_unauth_3`  AS SELECT `disbursement_analysis_unauth_2`.`ConsigneeID` AS `ConsigneeID`, `disbursement_analysis_unauth_2`.`ConsigneeName` AS `ConsigneeName`, `disbursement_analysis_unauth_2`.`BL` AS `BL`, `disbursement_analysis_unauth_2`.`HBL` AS `HBL`, `disbursement_analysis_unauth_2`.`ContainerNo` AS `ContainerNo`, `disbursement_analysis_unauth_2`.`Description` AS `Description`, `disbursement_analysis_unauth_2`.`TotalCashReceipt` AS `TotalCashReceipt`, `disbursement_analysis_unauth_2`.`ReceiptNo` AS `ReceiptNo`, `disbursement_analysis_unauth_2`.`AccountID` AS `AccountID`, `ledger_account`.`AccountName` AS `AccountName`, `disbursement_analysis_unauth_2`.`Expenditure` AS `Expenditure`, `disbursement_analysis_unauth_2`.`Username` AS `Username`, `disbursement_analysis_unauth_2`.`Date` AS `Date`, `disbursement_analysis_unauth_2`.`Time` AS `Time`, `disbursement_analysis_unauth_2`.`Status` AS `Status`, `disbursement_analysis_unauth_2`.`Type` AS `Type` FROM (`disbursement_analysis_unauth_2` join `ledger_account` on(`disbursement_analysis_unauth_2`.`AccountID` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `disbursement_analysis_unauth_4`
--
DROP TABLE IF EXISTS `disbursement_analysis_unauth_4`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `disbursement_analysis_unauth_4`  AS SELECT `disbursement_analysis_unauth_3`.`ConsigneeID` AS `ConsigneeID`, `disbursement_analysis_unauth_3`.`ConsigneeName` AS `ConsigneeName`, `disbursement_analysis_unauth_3`.`BL` AS `BL`, `disbursement_analysis_unauth_3`.`HBL` AS `HBL`, `disbursement_analysis_unauth_3`.`ContainerNo` AS `ContainerNo`, `disbursement_analysis_unauth_3`.`TotalCashReceipt` AS `TotalCashReceipt`, `disbursement_analysis_unauth_3`.`ReceiptNo` AS `ReceiptNo`, `disbursement_analysis_unauth_3`.`AccountID` AS `AccountID`, `disbursement_analysis_unauth_3`.`AccountName` AS `AccountName`, `disbursement_analysis_unauth_3`.`Expenditure` AS `Expenditure`, `disbursement_analysis_unauth_3`.`Username` AS `Username`, `disbursement_analysis_unauth_3`.`Date` AS `Date`, `disbursement_analysis_unauth_3`.`Time` AS `Time`, `disbursement_analysis_unauth_3`.`Status` AS `Status`, `disbursement_analysis_unauth_3`.`Type` AS `Type` FROM `disbursement_analysis_unauth_3` WHERE 1 ;

-- --------------------------------------------------------

--
-- Structure for view `disbursement_analysis_view`
--
DROP TABLE IF EXISTS `disbursement_analysis_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `disbursement_analysis_view`  AS SELECT `disbursement_analysis`.`ConsigneeID` AS `ConsigneeID`, `disbursement_analysis`.`BL` AS `BL`, `disbursement_analysis`.`HBL` AS `HBL`, `disbursement_analysis`.`ContainerNo` AS `ContainerNo`, `disbursement_analysis`.`TotalCashReceipt` AS `TotalCashReceipt`, `disbursement_analysis`.`ReceiptNo` AS `ReceiptNo`, `disbursement_analysis`.`AccountID` AS `AccountID`, `disbursement_analysis`.`Expenditure` AS `Expenditure`, `disbursement_analysis`.`Username` AS `Username`, `disbursement_analysis`.`Date` AS `Date`, `disbursement_analysis`.`Time` AS `Time`, `disbursement_analysis`.`Status` AS `Status`, `disbursement_analysis`.`Type` AS `Type` FROM `disbursement_analysis` WHERE 1 ;

-- --------------------------------------------------------

--
-- Structure for view `disbursement_analysis_view_0`
--
DROP TABLE IF EXISTS `disbursement_analysis_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `disbursement_analysis_view_0`  AS SELECT `container_main`.`Destination` AS `Destination`, `disbursement_analysis_view`.`BL` AS `BL`, `container_main`.`CarrierID` AS `CarrierID`, `disbursement_analysis_view`.`HBL` AS `HBL`, `disbursement_analysis_view`.`ContainerNo` AS `ContainerNo`, `disbursement_analysis_view`.`TotalCashReceipt` AS `TotalCashReceipt`, `disbursement_analysis_view`.`ReceiptNo` AS `ReceiptNo`, `disbursement_analysis_view`.`AccountID` AS `AccountID`, `disbursement_analysis_view`.`Expenditure` AS `Expenditure`, `disbursement_analysis_view`.`Username` AS `Username`, `disbursement_analysis_view`.`Date` AS `Date`, `disbursement_analysis_view`.`Time` AS `Time`, `disbursement_analysis_view`.`Status` AS `Status`, `disbursement_analysis_view`.`Type` AS `Type` FROM (`disbursement_analysis_view` join `container_main` on(`disbursement_analysis_view`.`BL` = `container_main`.`BL`)) ;

-- --------------------------------------------------------

--
-- Structure for view `disbursement_analysis_view_1`
--
DROP TABLE IF EXISTS `disbursement_analysis_view_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `disbursement_analysis_view_1`  AS SELECT `disbursement_analysis_view_0`.`Destination` AS `Destination`, `disbursement_analysis_view_0`.`BL` AS `BL`, `disbursement_analysis_view_0`.`CarrierID` AS `CarrierID`, `disbursement_analysis_view_0`.`HBL` AS `HBL`, `disbursement_analysis_view_0`.`ContainerNo` AS `ContainerNo`, `disbursement_analysis_view_0`.`TotalCashReceipt` AS `TotalCashReceipt`, `disbursement_analysis_view_0`.`ReceiptNo` AS `ReceiptNo`, `disbursement_analysis_view_0`.`AccountID` AS `AccountID`, `ledger_account`.`AccountName` AS `AccountName`, `disbursement_analysis_view_0`.`Expenditure` AS `Expenditure`, `disbursement_analysis_view_0`.`Username` AS `Username`, `disbursement_analysis_view_0`.`Date` AS `Date`, `disbursement_analysis_view_0`.`Time` AS `Time`, `disbursement_analysis_view_0`.`Status` AS `Status`, `disbursement_analysis_view_0`.`Type` AS `Type` FROM (`disbursement_analysis_view_0` join `ledger_account` on(`disbursement_analysis_view_0`.`AccountID` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `disbursement_analysis_view_2`
--
DROP TABLE IF EXISTS `disbursement_analysis_view_2`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `disbursement_analysis_view_2`  AS SELECT `disbursement_analysis_view_1`.`Destination` AS `Destination`, `disbursement_analysis_view_1`.`BL` AS `BL`, `disbursement_analysis_view_1`.`CarrierID` AS `CarrierID`, `ship_carrier`.`CarrierName` AS `Carrier`, `disbursement_analysis_view_1`.`TotalCashReceipt` AS `TotalCashReceipt`, `disbursement_analysis_view_1`.`ReceiptNo` AS `ReceiptNo`, `disbursement_analysis_view_1`.`AccountID` AS `AccountID`, `disbursement_analysis_view_1`.`AccountName` AS `AccountName`, `disbursement_analysis_view_1`.`Expenditure` AS `Expenditure`, `disbursement_analysis_view_1`.`Username` AS `Username`, `disbursement_analysis_view_1`.`Date` AS `Date`, `disbursement_analysis_view_1`.`Time` AS `Time`, `disbursement_analysis_view_1`.`Status` AS `Status`, `disbursement_analysis_view_1`.`Type` AS `Type` FROM (`disbursement_analysis_view_1` join `ship_carrier` on(`disbursement_analysis_view_1`.`CarrierID` = `ship_carrier`.`CarrierID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `disbursement_temp_analysis_view`
--
DROP TABLE IF EXISTS `disbursement_temp_analysis_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `disbursement_temp_analysis_view`  AS SELECT `disbursement_temp_analysis`.`AccountNo` AS `AccountNo`, `ledger_account`.`AccountName` AS `AccountName`, `disbursement_temp_analysis`.`BL` AS `BL`, `disbursement_temp_analysis`.`HouseBL` AS `HouseBL`, `disbursement_temp_analysis`.`ConsigneeID` AS `ConsigneeID`, `disbursement_temp_analysis`.`Amount` AS `Amount`, `disbursement_temp_analysis`.`Type` AS `Type`, `disbursement_temp_analysis`.`Status` AS `Status`, `disbursement_temp_analysis`.`Username` AS `Username`, `disbursement_temp_analysis`.`Time` AS `Time` FROM (`disbursement_temp_analysis` join `ledger_account`) WHERE `disbursement_temp_analysis`.`AccountNo` = `ledger_account`.`AccountNo` ;

-- --------------------------------------------------------

--
-- Structure for view `disbursement_temp_analysis_view_0`
--
DROP TABLE IF EXISTS `disbursement_temp_analysis_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `disbursement_temp_analysis_view_0`  AS SELECT `disbursement_temp_analysis_view`.`AccountNo` AS `AccountNo`, `disbursement_temp_analysis_view`.`AccountName` AS `AccountName`, `disbursement_temp_analysis_view`.`BL` AS `BL`, `disbursement_temp_analysis_view`.`HouseBL` AS `HouseBL`, `disbursement_temp_analysis_view`.`ConsigneeID` AS `ConsigneeID`, `consignee_main`.`FullName` AS `ConsigneeName`, `disbursement_temp_analysis_view`.`Amount` AS `Amount`, `disbursement_temp_analysis_view`.`Type` AS `Type`, `disbursement_temp_analysis_view`.`Status` AS `Status`, `disbursement_temp_analysis_view`.`Username` AS `Username`, `disbursement_temp_analysis_view`.`Time` AS `Time` FROM (`disbursement_temp_analysis_view` join `consignee_main` on(`disbursement_temp_analysis_view`.`ConsigneeID` = `consignee_main`.`ConsigneeID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `eta_web_track_view`
--
DROP TABLE IF EXISTS `eta_web_track_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `eta_web_track_view`  AS SELECT `eta_web_track`.`ConsignmentID` AS `ConsignmentID`, `eta_web_track`.`MainBL` AS `MainBL`, `eta_web_track`.`ETA` AS `ETA`, CASE WHEN `eta_web_track`.`Status` = 1 THEN 'On The Way' WHEN `eta_web_track`.`Status` = 2 THEN 'Arrived' WHEN `eta_web_track`.`Status` = 3 THEN 'Arrived and Stuffed' END AS `ArrivalStatus`, `eta_web_track`.`Status` AS `Status`, `eta_web_track`.`Username` AS `Username`, `eta_web_track`.`Time` AS `Time` FROM `eta_web_track` WHERE `eta_web_track`.`Status` = 1 OR `eta_web_track`.`Status` = 2 ;

-- --------------------------------------------------------

--
-- Structure for view `eta_web_track_view_0`
--
DROP TABLE IF EXISTS `eta_web_track_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `eta_web_track_view_0`  AS SELECT `eta_web_track_view`.`ConsignmentID` AS `ConsignmentID`, `eta_web_track_view`.`MainBL` AS `MainBL`, `manifestation_breakdown`.`HouseBL` AS `HouseBL`, `container_main_view`.`ETA` AS `ETA`, `container_main_view`.`SealNo` AS `SealNo`, `container_main_view`.`ContainerNo` AS `ContainerNo`, `container_main_view`.`SOB` AS `SOB`, `container_main_view`.`POL_Name` AS `POL_Name`, `container_main_view`.`POD_Name` AS `POD_Name`, `eta_web_track_view`.`ArrivalStatus` AS `ArrivalStatus`, `eta_web_track_view`.`Status` AS `Status`, `eta_web_track_view`.`Username` AS `Username`, `eta_web_track_view`.`Time` AS `Time` FROM ((`eta_web_track_view` join `manifestation_breakdown` on(`eta_web_track_view`.`MainBL` = `manifestation_breakdown`.`MainBL`)) join `container_main_view` on(`eta_web_track_view`.`MainBL` = `container_main_view`.`BL`)) ;

-- --------------------------------------------------------

--
-- Structure for view `e_delivery_order_request_view`
--
DROP TABLE IF EXISTS `e_delivery_order_request_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `e_delivery_order_request_view`  AS SELECT `e_delivery_order_request`.`HouseBL` AS `HouseBL`, `e_delivery_order_request`.`ReleaseType` AS `ReleaseType`, `e_delivery_order_request`.`Agency` AS `Agency`, `e_delivery_order_request`.`DocumentType` AS `DocumentType`, `e_delivery_order_request`.`UnstuffingType` AS `UnstuffingType`, `e_delivery_order_request`.`Status` AS `Status`, `e_delivery_order_request`.`Time` AS `Time`, 'DELIVERY_ORDER_REQUEST' AS `Type` FROM `e_delivery_order_request` WHERE 1 ;

-- --------------------------------------------------------

--
-- Structure for view `e_online_request`
--
DROP TABLE IF EXISTS `e_online_request`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `e_online_request`  AS SELECT `e_delivery_order_request_view`.`HouseBL` AS `HouseBL`, `e_delivery_order_request_view`.`Status` AS `Status`, `e_delivery_order_request_view`.`Type` AS `Type` FROM `e_delivery_order_request_view` ;

-- --------------------------------------------------------

--
-- Structure for view `e_payment_confirmation_view`
--
DROP TABLE IF EXISTS `e_payment_confirmation_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `e_payment_confirmation_view`  AS SELECT `e_payment_confirmation`.`HouseBL` AS `HouseBL`, `e_payment_confirmation`.`PaymentMode` AS `PaymentMode`, `e_payment_confirmation`.`PaymentDetails` AS `PaymentDetails`, `e_payment_confirmation`.`ImgUrl` AS `ImgUrl`, `e_payment_confirmation`.`ContactDetails` AS `ContactDetails`, `e_payment_confirmation`.`Time` AS `Time`, `e_payment_confirmation`.`Status` AS `Status`, 'PMT_CONFIRMATION' AS `Type` FROM `e_payment_confirmation` WHERE 1 ;

-- --------------------------------------------------------

--
-- Structure for view `fee_account_order_view`
--
DROP TABLE IF EXISTS `fee_account_order_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `fee_account_order_view`  AS SELECT `fee_account_order`.`AccountID` AS `AccountID`, `ledger_account`.`AccountName` AS `AccountName`, `fee_account_order`.`Order` AS `Order` FROM (`fee_account_order` join `ledger_account` on(`fee_account_order`.`AccountID` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `financial_statement_view`
--
DROP TABLE IF EXISTS `financial_statement_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `financial_statement_view`  AS SELECT `journal`.`AccountID` AS `AccountID`, `journal`.`SubAccountID` AS `SubAccountID`, `journal`.`Mode` AS `Mode`, `journal`.`TType` AS `TType`, `journal`.`ReceiptNo` AS `ReceiptNo`, `journal`.`Dr` AS `Dr`, `journal`.`Cr` AS `Cr`, `journal`.`Description` AS `Description`, `journal`.`Date` AS `Date`, `journal`.`Time` AS `Time`, `journal`.`Username` AS `Username`, `journal`.`Authorizer` AS `Authorizer`, `journal`.`BranchID` AS `BranchID`, `journal`.`Status` AS `Status`, `rpt_multi_values_0`.`LDate` AS `LDate`, `rpt_multi_values_0`.`Username` AS `RptUser` FROM (`journal` join `rpt_multi_values_0`) WHERE `journal`.`Date` <= `rpt_multi_values_0`.`LDate` ;

-- --------------------------------------------------------

--
-- Structure for view `financial_statement_view_0`
--
DROP TABLE IF EXISTS `financial_statement_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `financial_statement_view_0`  AS SELECT `financial_statement_view`.`AccountID` AS `AccountID`, round(sum(`financial_statement_view`.`Dr`),2) AS `TDr`, round(sum(`financial_statement_view`.`Cr`),2) AS `TCr`, round(sum(`financial_statement_view`.`Cr`) - sum(`financial_statement_view`.`Dr`),2) AS `TBal`, `financial_statement_view`.`LDate` AS `LDate`, `financial_statement_view`.`RptUser` AS `RptUser` FROM `financial_statement_view` GROUP BY `financial_statement_view`.`AccountID`, `financial_statement_view`.`RptUser` ;

-- --------------------------------------------------------

--
-- Structure for view `financial_statement_view_1`
--
DROP TABLE IF EXISTS `financial_statement_view_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `financial_statement_view_1`  AS SELECT `ledger_account_view`.`ControlID` AS `ControlID`, `ledger_account_view`.`ControlName` AS `ControlName`, `ledger_account_view`.`CategoryID` AS `CategoryID`, `ledger_account_view`.`CategoryName` AS `CategoryName`, `ledger_account_view`.`SubCategoryID` AS `SubCategoryID`, `ledger_account_view`.`SubCategoryName` AS `SubCategoryName`, `ledger_account_view`.`Class` AS `Class`, `ledger_account_view`.`Nature` AS `Nature`, `ledger_account_view`.`Type` AS `Type`, `financial_statement_view_0`.`AccountID` AS `AccountID`, `ledger_account_view`.`AccountName` AS `AccountName`, `financial_statement_view_0`.`TDr` AS `TDr`, `financial_statement_view_0`.`TCr` AS `TCr`, `financial_statement_view_0`.`TBal` AS `TBal`, `financial_statement_view_0`.`LDate` AS `LDate`, `financial_statement_view_0`.`RptUser` AS `RptUser` FROM (`financial_statement_view_0` left join `ledger_account_view` on(`financial_statement_view_0`.`AccountID` = `ledger_account_view`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `gateout_pending_consignment`
--
DROP TABLE IF EXISTS `gateout_pending_consignment`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `gateout_pending_consignment`  AS SELECT `container_details`.`ConsignmentID` AS `ConsignmentID`, `container_details`.`BL` AS `BL`, `container_main`.`Destination` AS `Destination`, `container_details`.`SealNo` AS `SealNo`, `container_details`.`ContainerNo` AS `ContainerNo`, `container_details`.`ContainerSize` AS `ContainerSize`, `container_details`.`Weight` AS `Weight`, `container_details`.`HandlingCost` AS `HandlingCost`, `container_details`.`Username` AS `Username`, `container_details`.`BranchID` AS `BranchID`, `container_details`.`Date` AS `Date`, `container_details`.`Time` AS `Time`, `container_details`.`Status` AS `Status` FROM (`container_details` left join `container_main` on(`container_details`.`BL` = `container_main`.`BL`)) WHERE `container_details`.`Status` = 3 ;

-- --------------------------------------------------------

--
-- Structure for view `general_accounts`
--
DROP TABLE IF EXISTS `general_accounts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `general_accounts`  AS SELECT `ledger_account`.`AccountNo` AS `AccountNo`, `ledger_account`.`AccountName` AS `AccountName`, `ledger_account`.`Type` AS `AccountType` FROM `ledger_account` ;

-- --------------------------------------------------------

--
-- Structure for view `general_ledger_balances`
--
DROP TABLE IF EXISTS `general_ledger_balances`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `general_ledger_balances`  AS SELECT `journal`.`AccountID` AS `AccountID`, `journal`.`SubAccountID` AS `SubAccountID`, `journal`.`Mode` AS `Mode`, `journal`.`TType` AS `TType`, round(sum(`journal`.`Dr`),2) AS `Dr`, round(sum(`journal`.`Cr`),2) AS `Cr`, max(`journal`.`Date`) AS `LDate`, `journal`.`BranchID` AS `BranchID` FROM `journal` GROUP BY `journal`.`AccountID`, `journal`.`BranchID` ;

-- --------------------------------------------------------

--
-- Structure for view `general_ledger_balances_0`
--
DROP TABLE IF EXISTS `general_ledger_balances_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `general_ledger_balances_0`  AS SELECT `ledger_account_view`.`ControlID` AS `ControlID`, `ledger_account_view`.`ControlName` AS `ControlName`, `ledger_account_view`.`CategoryID` AS `CategoryID`, `ledger_account_view`.`Class` AS `Class`, `general_ledger_balances`.`AccountID` AS `AccountID`, `ledger_account_view`.`AccountName` AS `AccountName`, `general_ledger_balances`.`SubAccountID` AS `SubAccountID`, `general_ledger_balances`.`Mode` AS `Mode`, `general_ledger_balances`.`TType` AS `TType`, `general_ledger_balances`.`Dr` AS `Dr`, `general_ledger_balances`.`Cr` AS `Cr`, round(`general_ledger_balances`.`Cr` - `general_ledger_balances`.`Dr`,2) AS `Balance`, `general_ledger_balances`.`LDate` AS `LDate`, `general_ledger_balances`.`BranchID` AS `BranchID` FROM (`general_ledger_balances` join `ledger_account_view` on(`general_ledger_balances`.`AccountID` = `ledger_account_view`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `gl_statement`
--
DROP TABLE IF EXISTS `gl_statement`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `gl_statement`  AS SELECT `journal`.`AccountID` AS `AccountID`, `ledger_account`.`AccountName` AS `AccountName`, `journal`.`SubAccountID` AS `SubAccountID`, `journal`.`Mode` AS `Mode`, `journal`.`TType` AS `TType`, `journal`.`ReceiptNo` AS `ReceiptNo`, round(`journal`.`Dr`,2) AS `Dr`, round(`journal`.`Cr`,2) AS `Cr`, `journal`.`Description` AS `Description`, `journal`.`Date` AS `Date`, `journal`.`Time` AS `Time`, `journal`.`Username` AS `Username`, `journal`.`Authorizer` AS `Authorizer`, `journal`.`BranchID` AS `BranchID`, `journal`.`Status` AS `Status` FROM (`journal` join `ledger_account` on(`journal`.`AccountID` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `gl_statement_1`
--
DROP TABLE IF EXISTS `gl_statement_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `gl_statement_1`  AS SELECT `gl_statement`.`AccountID` AS `AccountID`, `gl_statement`.`AccountName` AS `AccountName`, `gl_statement`.`SubAccountID` AS `SubAccountID`, `gl_statement`.`Mode` AS `Mode`, `gl_statement`.`TType` AS `TType`, `gl_statement`.`ReceiptNo` AS `ReceiptNo`, `gl_statement`.`Dr` AS `Dr`, `gl_statement`.`Cr` AS `Cr`, `gl_statement`.`Description` AS `Description`, `gl_statement`.`Date` AS `Date`, `gl_statement`.`Time` AS `Time`, `gl_statement`.`Username` AS `Username`, `gl_statement`.`Authorizer` AS `Authorizer`, `gl_statement`.`BranchID` AS `BranchID`, `gl_statement`.`Status` AS `Status` FROM `gl_statement` ;

-- --------------------------------------------------------

--
-- Structure for view `gl_statement_sub_account`
--
DROP TABLE IF EXISTS `gl_statement_sub_account`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `gl_statement_sub_account`  AS SELECT `gl_statement`.`AccountID` AS `AccountID`, `gl_statement`.`AccountName` AS `AccountName`, `gl_statement`.`SubAccountID` AS `SubAccountID`, `ledger_account`.`AccountName` AS `SubAccountName`, `ledger_account`.`Type` AS `Type`, `gl_statement`.`Mode` AS `Mode`, `gl_statement`.`TType` AS `TType`, `gl_statement`.`ReceiptNo` AS `ReceiptNo`, `gl_statement`.`Dr` AS `Dr`, `gl_statement`.`Cr` AS `Cr`, `gl_statement`.`Description` AS `Description`, `gl_statement`.`Date` AS `Date`, `gl_statement`.`Time` AS `Time`, `gl_statement`.`Username` AS `Username`, `gl_statement`.`Authorizer` AS `Authorizer`, `gl_statement`.`BranchID` AS `BranchID`, `gl_statement`.`Status` AS `Status` FROM (`gl_statement` left join `ledger_account` on(`gl_statement`.`SubAccountID` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `graph_test`
--
DROP TABLE IF EXISTS `graph_test`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `graph_test`  AS SELECT `manifestation_breakdown_view`.`ConsignmentID` AS `ConsignmentID`, `manifestation_breakdown_view`.`MainBL` AS `MainBL`, `manifestation_breakdown_view`.`HouseBL` AS `HouseBL`, `manifestation_breakdown_view`.`ConsigneeID` AS `ConsigneeID`, `manifestation_breakdown_view`.`FullName` AS `FullName`, `manifestation_breakdown_view`.`TFee` AS `TFee`, `manifestation_breakdown_view`.`Consigenee2_ID` AS `Consigenee2_ID`, `manifestation_breakdown_view`.`Description` AS `Description`, `manifestation_breakdown_view`.`Weight` AS `Weight`, `manifestation_breakdown_view`.`Package` AS `Package`, `manifestation_breakdown_view`.`Unit` AS `Unit`, `manifestation_breakdown_view`.`Username` AS `Username`, `manifestation_breakdown_view`.`TDate` AS `TDate`, `manifestation_breakdown_view`.`Date` AS `Date`, month(`manifestation_breakdown_view`.`Date`) AS `DMonth`, left(monthname(`manifestation_breakdown_view`.`Date`),3) AS `MonthName`, year(`manifestation_breakdown_view`.`Date`) AS `YearName`, `manifestation_breakdown_view`.`Time` AS `Time`, `manifestation_breakdown_view`.`Status` AS `Status` FROM `manifestation_breakdown_view` ;

-- --------------------------------------------------------

--
-- Structure for view `graph_test_0`
--
DROP TABLE IF EXISTS `graph_test_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `graph_test_0`  AS SELECT `graph_test`.`ConsignmentID` AS `ConsignmentID`, `graph_test`.`MainBL` AS `MainBL`, `graph_test`.`HouseBL` AS `HouseBL`, `graph_test`.`ConsigneeID` AS `ConsigneeID`, `graph_test`.`FullName` AS `FullName`, `graph_test`.`TFee` AS `TFee`, `graph_test`.`Weight` AS `Weight`, `graph_test`.`Unit` AS `Unit`, `graph_test`.`Username` AS `Username`, `graph_test`.`TDate` AS `TDate`, `graph_test`.`Date` AS `Date`, `graph_test`.`DMonth` AS `DMonth`, `graph_test`.`MonthName` AS `MonthName`, `graph_test`.`YearName` AS `YearName`, concat(`graph_test`.`MonthName`,' ',`graph_test`.`YearName`) AS `MonthYear`, `graph_test`.`Time` AS `Time`, `graph_test`.`Status` AS `Status` FROM `graph_test` ;

-- --------------------------------------------------------

--
-- Structure for view `graph_test_1`
--
DROP TABLE IF EXISTS `graph_test_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `graph_test_1`  AS SELECT `graph_test_0`.`DMonth` AS `DMonth`, `graph_test_0`.`MonthName` AS `MonthName`, `graph_test_0`.`YearName` AS `YearName`, `graph_test_0`.`MonthYear` AS `MonthYear`, count(distinct `graph_test_0`.`MainBL`) AS `TWeight` FROM `graph_test_0` GROUP BY `graph_test_0`.`MonthYear` ORDER BY `graph_test_0`.`YearName` ASC, `graph_test_0`.`DMonth` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `group_members_view`
--
DROP TABLE IF EXISTS `group_members_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `group_members_view`  AS SELECT `group_members`.`GroupID` AS `GroupID`, `groups`.`GroupName` AS `GroupName`, `group_members`.`MemberID` AS `MemberID`, `student_list`.`FullName` AS `MemberName`, `group_members`.`Username` AS `Username`, `group_members`.`Date` AS `Date`, `group_members`.`Time` AS `Time`, `group_members`.`Status` AS `Status` FROM ((`group_members` join `groups` on(`group_members`.`GroupID` = `groups`.`GroupID`)) join `student_list` on(`group_members`.`MemberID` = `student_list`.`StudentID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `handling_charge_view`
--
DROP TABLE IF EXISTS `handling_charge_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `handling_charge_view`  AS SELECT `handling_charge`.`AccountNo` AS `AccountNo`, `ledger_account`.`AccountName` AS `AccountName`, `handling_charge`.`Amount` AS `Amount`, `handling_charge`.`POrder` AS `PaymentOrder`, `handling_charge`.`Username` AS `Username`, `handling_charge`.`Time` AS `Time` FROM (`handling_charge` join `ledger_account` on(`handling_charge`.`AccountNo` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `hbl_invoice_consignee_temp_view`
--
DROP TABLE IF EXISTS `hbl_invoice_consignee_temp_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `hbl_invoice_consignee_temp_view`  AS SELECT `hbl_invoice_consignee_temp`.`ConsignmentID` AS `ConsignmentID`, `hbl_invoice_consignee_temp`.`MainBL` AS `MainBL`, `hbl_invoice_consignee_temp`.`HouseBL` AS `HouseBL`, `hbl_invoice_consignee_temp`.`ConsigneeID` AS `ConsigneeID`, `consignee_main`.`FullName` AS `FullName`, `manifestation_breakdown`.`Description` AS `Description`, `manifestation_breakdown`.`Weight` AS `Weight`, `manifestation_breakdown`.`Package` AS `Package`, `manifestation_breakdown`.`Unit` AS `Unit`, `hbl_invoice_consignee_temp`.`AccountNo` AS `AccountNo`, `ledger_account`.`AccountName` AS `AccountName`, round(`hbl_invoice_consignee_temp`.`Amount`,2) AS `Amount`, round(`hbl_invoice_consignee_temp`.`GetFundNHIL` * `hbl_invoice_consignee_temp`.`Amount`,2) AS `GetFund`, round(`hbl_invoice_consignee_temp`.`GetFundNHIL` * `hbl_invoice_consignee_temp`.`Amount` + `hbl_invoice_consignee_temp`.`Amount`,2) AS `SubTotal`, `hbl_invoice_consignee_temp`.`VAT` AS `VAT`, `hbl_invoice_consignee_temp`.`Date` AS `Date`, `hbl_invoice_consignee_temp`.`Time` AS `Time`, `hbl_invoice_consignee_temp`.`Username` AS `Username` FROM (((`hbl_invoice_consignee_temp` join `consignee_main` on(`hbl_invoice_consignee_temp`.`ConsigneeID` = `consignee_main`.`ConsigneeID`)) join `manifestation_breakdown` on(`hbl_invoice_consignee_temp`.`ConsigneeID` = `manifestation_breakdown`.`ConsigneeID` and `hbl_invoice_consignee_temp`.`MainBL` = `manifestation_breakdown`.`MainBL` and `hbl_invoice_consignee_temp`.`HouseBL` = `manifestation_breakdown`.`HouseBL` and `hbl_invoice_consignee_temp`.`ConsignmentID` = `manifestation_breakdown`.`ConsignmentID`)) join `ledger_account` on(`hbl_invoice_consignee_temp`.`AccountNo` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `hbl_invoice_consignee_temp_view_0`
--
DROP TABLE IF EXISTS `hbl_invoice_consignee_temp_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `hbl_invoice_consignee_temp_view_0`  AS SELECT `hbl_invoice_consignee_temp_view`.`ConsignmentID` AS `ConsignmentID`, `hbl_invoice_consignee_temp_view`.`MainBL` AS `MainBL`, `hbl_invoice_consignee_temp_view`.`HouseBL` AS `HouseBL`, `hbl_invoice_consignee_temp_view`.`ConsigneeID` AS `ConsigneeID`, `hbl_invoice_consignee_temp_view`.`FullName` AS `FullName`, `hbl_invoice_consignee_temp_view`.`Description` AS `Description`, `hbl_invoice_consignee_temp_view`.`Weight` AS `Weight`, `hbl_invoice_consignee_temp_view`.`Package` AS `Package`, `hbl_invoice_consignee_temp_view`.`Unit` AS `Unit`, `hbl_invoice_consignee_temp_view`.`AccountNo` AS `AccountNo`, `hbl_invoice_consignee_temp_view`.`AccountName` AS `AccountName`, `hbl_invoice_consignee_temp_view`.`Amount` AS `Amount`, `hbl_invoice_consignee_temp_view`.`GetFund` AS `GetFund`, `hbl_invoice_consignee_temp_view`.`SubTotal` AS `SubTotal`, round(`hbl_invoice_consignee_temp_view`.`VAT` * `hbl_invoice_consignee_temp_view`.`SubTotal`,2) AS `VAT`, round(`hbl_invoice_consignee_temp_view`.`VAT` * `hbl_invoice_consignee_temp_view`.`SubTotal` + `hbl_invoice_consignee_temp_view`.`SubTotal`,2) AS `SubTotalTax`, `charge_taxes`.`GetFund` AS `GetFundPcnt`, `charge_taxes`.`VAT` AS `VATPcnt`, `hbl_invoice_consignee_temp_view`.`Date` AS `Date`, `hbl_invoice_consignee_temp_view`.`Time` AS `Time`, `hbl_invoice_consignee_temp_view`.`Username` AS `Username` FROM (`hbl_invoice_consignee_temp_view` join `charge_taxes`) ;

-- --------------------------------------------------------

--
-- Structure for view `hbl_invoice_view`
--
DROP TABLE IF EXISTS `hbl_invoice_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `hbl_invoice_view`  AS SELECT `hbl_invoice`.`ConsignmentID` AS `ConsignmentID`, `hbl_invoice`.`MainBL` AS `MainBL`, `hbl_invoice`.`HouseBL` AS `HouseBL`, `hbl_invoice`.`ConsigneeID` AS `ConsigneeID`, 'No Description' AS `Description`, `hbl_invoice`.`ReceiptNo` AS `ReceiptNo`, `hbl_invoice`.`AccountNo` AS `AccountNo`, `hbl_invoice`.`Fee` AS `Fee`, `hbl_invoice`.`GetFundNHIL` AS `GetFundNHIL`, `hbl_invoice`.`VAT` AS `VAT`, 'BL' AS `Stamp`, `hbl_invoice`.`Date` AS `Date`, `hbl_invoice`.`Time` AS `Time`, `hbl_invoice`.`Username` AS `Username`, `hbl_invoice`.`Status` AS `Status` FROM `hbl_invoice`union select '' AS `ConsignmentID`,`other_invoice`.`MainBL` AS `MainBL`,`other_invoice`.`HouseBL` AS `HouseBL`,`other_invoice`.`ClientID` AS `ClientID`,`other_invoice`.`Description` AS `Description`,`other_invoice`.`ReceiptNo` AS `ReceiptNo`,`other_invoice`.`AccountNo` AS `AccountNo`,`other_invoice`.`Amount` AS `Amount`,`other_invoice`.`GetFundNHIL` AS `GetFundNHIL`,`other_invoice`.`VAT` AS `VAT`,`other_invoice`.`Stamp` AS `Stamp`,`other_invoice`.`Date` AS `Date`,`other_invoice`.`Time` AS `Time`,`other_invoice`.`Username` AS `Username`,`other_invoice`.`Status` AS `Status` from `other_invoice`  ;

-- --------------------------------------------------------

--
-- Structure for view `hbl_invoice_view_0`
--
DROP TABLE IF EXISTS `hbl_invoice_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `hbl_invoice_view_0`  AS SELECT `hbl_invoice_view`.`ConsignmentID` AS `ConsignmentID`, `hbl_invoice_view`.`MainBL` AS `MainBL`, `hbl_invoice_view`.`HouseBL` AS `HouseBL`, `hbl_invoice_view`.`ConsigneeID` AS `ConsigneeID`, `hbl_invoice_view`.`ReceiptNo` AS `ReceiptNo`, `hbl_invoice_view`.`AccountNo` AS `AccountNo`, round(sum(`hbl_invoice_view`.`Fee`),2) AS `TFee`, `hbl_invoice_view`.`GetFundNHIL` AS `GetFundNHIL`, `hbl_invoice_view`.`VAT` AS `VAT`, `hbl_invoice_view`.`Date` AS `Date`, `hbl_invoice_view`.`Time` AS `Time`, `hbl_invoice_view`.`Username` AS `Username`, `hbl_invoice_view`.`Status` AS `Status` FROM `hbl_invoice_view` GROUP BY `hbl_invoice_view`.`ConsignmentID`, `hbl_invoice_view`.`MainBL`, `hbl_invoice_view`.`HouseBL`, `hbl_invoice_view`.`ConsigneeID`, `hbl_invoice_view`.`ReceiptNo` ;

-- --------------------------------------------------------

--
-- Structure for view `hbl_invoice_view_0_0`
--
DROP TABLE IF EXISTS `hbl_invoice_view_0_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `hbl_invoice_view_0_0`  AS SELECT `hbl_invoice_view`.`ConsignmentID` AS `ConsignmentID`, `hbl_invoice_view`.`MainBL` AS `MainBL`, `hbl_invoice_view`.`HouseBL` AS `HouseBL`, `hbl_invoice_view`.`ConsigneeID` AS `ConsigneeID`, `hbl_invoice_view`.`ReceiptNo` AS `ReceiptNo`, `hbl_invoice_view`.`AccountNo` AS `AccountNo`, `ledger_account`.`AccountName` AS `AccountName`, `hbl_invoice_view`.`Description` AS `ItemDescription`, `student_fee`.`Description` AS `Description`, `hbl_invoice_view`.`Fee` AS `Fee`, `hbl_invoice_view`.`GetFundNHIL` AS `GetFundNHIL`, round(`hbl_invoice_view`.`Fee` * `hbl_invoice_view`.`GetFundNHIL`,2) AS `GetVal`, round(`hbl_invoice_view`.`Fee` + `hbl_invoice_view`.`Fee` * `hbl_invoice_view`.`GetFundNHIL`,2) AS `SubTotal`, `hbl_invoice_view`.`VAT` AS `VAT`, `hbl_invoice_view`.`Stamp` AS `Stamp`, `hbl_invoice_view`.`Date` AS `Date`, `hbl_invoice_view`.`Time` AS `Time`, `hbl_invoice_view`.`Username` AS `Username`, `hbl_invoice_view`.`Status` AS `Status` FROM ((`hbl_invoice_view` left join `student_fee` on(`hbl_invoice_view`.`ReceiptNo` = `student_fee`.`ReceiptNo` and `hbl_invoice_view`.`AccountNo` = `student_fee`.`AccountNo` and `hbl_invoice_view`.`ConsigneeID` = `student_fee`.`StudentID`)) join `ledger_account` on(`hbl_invoice_view`.`AccountNo` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `hbl_invoice_view_0_1`
--
DROP TABLE IF EXISTS `hbl_invoice_view_0_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `hbl_invoice_view_0_1`  AS SELECT `hbl_invoice_view_0_0`.`ConsignmentID` AS `ConsignmentID`, `hbl_invoice_view_0_0`.`MainBL` AS `MainBL`, `hbl_invoice_view_0_0`.`HouseBL` AS `HouseBL`, `hbl_invoice_view_0_0`.`ConsigneeID` AS `ConsigneeID`, `consignee_main`.`FullName` AS `FullName`, `hbl_invoice_view_0_0`.`ReceiptNo` AS `ReceiptNo`, `hbl_invoice_view_0_0`.`AccountNo` AS `AccountNo`, `hbl_invoice_view_0_0`.`AccountName` AS `AccountName`, `hbl_invoice_view_0_0`.`ItemDescription` AS `ItemDescription`, `hbl_invoice_view_0_0`.`Description` AS `Description`, `hbl_invoice_view_0_0`.`Fee` AS `Fee`, `hbl_invoice_view_0_0`.`GetFundNHIL` AS `GetFundNHIL`, `hbl_invoice_view_0_0`.`GetVal` AS `GetVal`, `hbl_invoice_view_0_0`.`SubTotal` AS `SubTotal`, `hbl_invoice_view_0_0`.`VAT` AS `VAT`, round(`hbl_invoice_view_0_0`.`SubTotal` * `hbl_invoice_view_0_0`.`VAT`,2) AS `VATVal`, round(`hbl_invoice_view_0_0`.`SubTotal` + `hbl_invoice_view_0_0`.`SubTotal` * `hbl_invoice_view_0_0`.`VAT`,2) AS `TotalCharges`, `hbl_invoice_view_0_0`.`Stamp` AS `Stamp`, `hbl_invoice_view_0_0`.`Date` AS `Date`, `hbl_invoice_view_0_0`.`Time` AS `Time`, `hbl_invoice_view_0_0`.`Username` AS `Username`, `hbl_invoice_view_0_0`.`Status` AS `Status` FROM (`hbl_invoice_view_0_0` join `consignee_main` on(`hbl_invoice_view_0_0`.`ConsigneeID` = `consignee_main`.`ConsigneeID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `hbl_invoice_view_1`
--
DROP TABLE IF EXISTS `hbl_invoice_view_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `hbl_invoice_view_1`  AS SELECT `hbl_invoice_view_0`.`ConsignmentID` AS `ConsignmentID`, `container_main`.`ShipperID` AS `ShipperID`, `hbl_invoice_view_0`.`MainBL` AS `MainBL`, `hbl_invoice_view_0`.`HouseBL` AS `HouseBL`, `hbl_invoice_view_0`.`ConsigneeID` AS `ConsigneeID`, `consignee_main`.`FullName` AS `FullName`, `manifestation_breakdown`.`Consigenee2_ID` AS `Consignee2_ID`, `hbl_invoice_view_0`.`ReceiptNo` AS `ReceiptNo`, `hbl_invoice_view_0`.`AccountNo` AS `AccountNo`, `hbl_invoice_view_0`.`TFee` AS `TFee`, `hbl_invoice_view_0`.`GetFundNHIL` AS `GetFundNHIL`, `hbl_invoice_view_0`.`VAT` AS `VAT`, round(`hbl_invoice_view_0`.`TFee` * `hbl_invoice_view_0`.`GetFundNHIL`,2) AS `GTFVal`, round(`hbl_invoice_view_0`.`TFee` * `hbl_invoice_view_0`.`GetFundNHIL` + `hbl_invoice_view_0`.`TFee`,2) AS `SubTotal`, round((`hbl_invoice_view_0`.`TFee` * `hbl_invoice_view_0`.`GetFundNHIL` + `hbl_invoice_view_0`.`TFee`) * `hbl_invoice_view_0`.`VAT`,2) AS `VATVal`, `hbl_invoice_view_0`.`Date` AS `Date`, `hbl_invoice_view_0`.`Time` AS `Time`, `hbl_invoice_view_0`.`Username` AS `Username`, `kaina`.`FullName` AS `UserFullName`, `hbl_invoice_view_0`.`Status` AS `Status` FROM ((((`hbl_invoice_view_0` left join `manifestation_breakdown` on(`hbl_invoice_view_0`.`ConsignmentID` = `manifestation_breakdown`.`ConsignmentID` and `hbl_invoice_view_0`.`MainBL` = `manifestation_breakdown`.`MainBL` and `hbl_invoice_view_0`.`HouseBL` = `manifestation_breakdown`.`HouseBL` and `hbl_invoice_view_0`.`ConsigneeID` = `manifestation_breakdown`.`ConsigneeID`)) left join `container_main` on(`hbl_invoice_view_0`.`ConsignmentID` = `container_main`.`ConsignmentID`)) join `consignee_main` on(`hbl_invoice_view_0`.`ConsigneeID` = `consignee_main`.`ConsigneeID`)) join `kaina` on(`hbl_invoice_view_0`.`Username` = `kaina`.`ID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `ie_transaction_journal`
--
DROP TABLE IF EXISTS `ie_transaction_journal`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ie_transaction_journal`  AS SELECT `journal`.`AccountID` AS `AccountID`, `journal`.`SubAccountID` AS `SubAccountID`, `active_ie`.`AccountID` AS `MainIE`, `journal`.`Mode` AS `Mode`, `journal`.`TType` AS `TType`, `journal`.`ReceiptNo` AS `ReceiptNo`, `journal`.`Dr` AS `Dr`, `journal`.`Cr` AS `Cr`, `journal`.`Description` AS `Description`, `journal`.`Date` AS `Date`, `journal`.`Time` AS `Time`, `journal`.`Username` AS `Username`, `journal`.`BranchID` AS `BranchID`, `journal`.`Status` AS `Status` FROM (`journal` join `active_ie` on(`journal`.`AccountID` = `active_ie`.`AccountID`)) WHERE `journal`.`Status` = '1' ;

-- --------------------------------------------------------

--
-- Structure for view `ie_transaction_journal_0`
--
DROP TABLE IF EXISTS `ie_transaction_journal_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ie_transaction_journal_0`  AS SELECT `ie_transaction_journal`.`AccountID` AS `ControlID`, `ie_transaction_journal`.`SubAccountID` AS `AccountID`, `ledger_account_view`.`AccountName` AS `AccountName`, `ledger_account_view`.`CategoryID` AS `CategoryID`, `ledger_account_view`.`CategoryName` AS `CategoryName`, `ledger_account_view`.`SubCategoryID` AS `SubCategoryID`, `ledger_account_view`.`SubCategoryName` AS `SubCategoryName`, `ie_transaction_journal`.`MainIE` AS `MainIE`, `ledger_account_view`.`Type` AS `Type`, `ie_transaction_journal`.`Mode` AS `Mode`, `ie_transaction_journal`.`TType` AS `TType`, `ie_transaction_journal`.`ReceiptNo` AS `ReceiptNo`, `ie_transaction_journal`.`Dr` AS `Dr`, `ie_transaction_journal`.`Cr` AS `Cr`, `ie_transaction_journal`.`Description` AS `Description`, `ie_transaction_journal`.`Date` AS `Date`, `ie_transaction_journal`.`Time` AS `Time`, `ie_transaction_journal`.`Username` AS `Username`, `ie_transaction_journal`.`BranchID` AS `BranchID` FROM (`ie_transaction_journal` join `ledger_account_view` on(`ie_transaction_journal`.`SubAccountID` = `ledger_account_view`.`AccountNo`)) WHERE `ledger_account_view`.`Type` <> 'GL' ;

-- --------------------------------------------------------

--
-- Structure for view `ie_transaction_journal_1`
--
DROP TABLE IF EXISTS `ie_transaction_journal_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ie_transaction_journal_1`  AS SELECT `ie_transaction_journal_0`.`ControlID` AS `ControlID`, `ie_transaction_journal_0`.`AccountID` AS `AccountID`, `ie_transaction_journal_0`.`AccountName` AS `AccountName`, `ie_transaction_journal_0`.`CategoryID` AS `CategoryID`, `ie_transaction_journal_0`.`CategoryName` AS `CategoryName`, `ie_transaction_journal_0`.`SubCategoryID` AS `SubCategoryID`, `ie_transaction_journal_0`.`SubCategoryName` AS `SubCategoryName`, `ie_transaction_journal_0`.`MainIE` AS `MainIE`, `ie_transaction_journal_0`.`Type` AS `Type`, `ie_transaction_journal_0`.`Mode` AS `Mode`, `ie_transaction_journal_0`.`TType` AS `TType`, `ie_transaction_journal_0`.`ReceiptNo` AS `ReceiptNo`, `ie_transaction_journal_0`.`Dr` AS `Dr`, `ie_transaction_journal_0`.`Cr` AS `Cr`, `ie_transaction_journal_0`.`Description` AS `Description`, `ie_transaction_journal_0`.`Date` AS `Date`, `ie_transaction_journal_0`.`Time` AS `Time`, `ie_transaction_journal_0`.`Username` AS `Username`, `ie_transaction_journal_0`.`BranchID` AS `BranchID`, `rpt_multi_values_0`.`FDate` AS `FDate`, `rpt_multi_values_0`.`LDate` AS `LDate`, `rpt_multi_values_0`.`Username` AS `RptUser` FROM (`ie_transaction_journal_0` join `rpt_multi_values_0`) WHERE `ie_transaction_journal_0`.`Date` between `rpt_multi_values_0`.`FDate` and `rpt_multi_values_0`.`LDate` ;

-- --------------------------------------------------------

--
-- Structure for view `ie_transaction_journal_2`
--
DROP TABLE IF EXISTS `ie_transaction_journal_2`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ie_transaction_journal_2`  AS SELECT `ie_transaction_journal_1`.`ControlID` AS `ControlID`, `ie_transaction_journal_1`.`AccountID` AS `AccountID`, `ie_transaction_journal_1`.`AccountName` AS `AccountName`, `ie_transaction_journal_1`.`Type` AS `Type`, `ie_transaction_journal_1`.`CategoryID` AS `CategoryID`, `ie_transaction_journal_1`.`CategoryName` AS `CategoryName`, `ie_transaction_journal_1`.`SubCategoryID` AS `SubCategoryID`, `ie_transaction_journal_1`.`SubCategoryName` AS `SubCategoryName`, `ie_transaction_journal_1`.`MainIE` AS `MainIE`, round(sum(`ie_transaction_journal_1`.`Dr`),2) AS `TDr`, round(sum(`ie_transaction_journal_1`.`Cr`),2) AS `TCr`, round(sum(`ie_transaction_journal_1`.`Cr`) - sum(`ie_transaction_journal_1`.`Dr`),2) AS `TBal`, `ie_transaction_journal_1`.`BranchID` AS `BranchID`, `ie_transaction_journal_1`.`FDate` AS `FDate`, `ie_transaction_journal_1`.`LDate` AS `LDate`, `ie_transaction_journal_1`.`RptUser` AS `RptUser` FROM `ie_transaction_journal_1` GROUP BY `ie_transaction_journal_1`.`AccountID`, `ie_transaction_journal_1`.`BranchID`, `ie_transaction_journal_1`.`RptUser` ;

-- --------------------------------------------------------

--
-- Structure for view `ie_transaction_journal_balance`
--
DROP TABLE IF EXISTS `ie_transaction_journal_balance`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ie_transaction_journal_balance`  AS SELECT `ie_transaction_journal`.`AccountID` AS `AccountID`, `ie_transaction_journal`.`SubAccountID` AS `SubAccountID`, round(sum(`ie_transaction_journal`.`Dr`),2) AS `TDr`, round(sum(`ie_transaction_journal`.`Cr`),2) AS `TCr`, `ie_transaction_journal`.`BranchID` AS `BranchID` FROM `ie_transaction_journal` GROUP BY `ie_transaction_journal`.`AccountID`, `ie_transaction_journal`.`SubAccountID` ;

-- --------------------------------------------------------

--
-- Structure for view `ie_transaction_journal_balance_0`
--
DROP TABLE IF EXISTS `ie_transaction_journal_balance_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ie_transaction_journal_balance_0`  AS SELECT `ie_transaction_journal_balance`.`AccountID` AS `AccountID`, `ie_transaction_journal_balance`.`SubAccountID` AS `SubAccountID`, `ledger_account`.`AccountName` AS `AccountName`, `ledger_account`.`Type` AS `Type`, `ie_transaction_journal_balance`.`TDr` AS `TDr`, `ie_transaction_journal_balance`.`TCr` AS `TCr`, round(`ie_transaction_journal_balance`.`TCr` - `ie_transaction_journal_balance`.`TDr`,2) AS `TBal`, `ie_transaction_journal_balance`.`BranchID` AS `BranchID` FROM (`ie_transaction_journal_balance` join `ledger_account` on(`ie_transaction_journal_balance`.`SubAccountID` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `ie_transaction_journal_general`
--
DROP TABLE IF EXISTS `ie_transaction_journal_general`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ie_transaction_journal_general`  AS SELECT `ie_transaction_journal_2`.`ControlID` AS `ControlID`, `ie_transaction_journal_2`.`AccountID` AS `AccountID`, `ie_transaction_journal_2`.`AccountName` AS `AccountName`, `ie_transaction_journal_2`.`CategoryID` AS `CategoryID`, `ie_transaction_journal_2`.`CategoryName` AS `CategoryName`, `ie_transaction_journal_2`.`SubCategoryID` AS `SubCategoryID`, `ie_transaction_journal_2`.`SubCategoryName` AS `SubCategoryName`, `ie_transaction_journal_2`.`MainIE` AS `MainIE`, round(sum(`ie_transaction_journal_2`.`TDr`),2) AS `TDr`, round(sum(`ie_transaction_journal_2`.`TCr`),2) AS `TCr`, round(sum(`ie_transaction_journal_2`.`TCr`) - sum(`ie_transaction_journal_2`.`TDr`),2) AS `TBal`, `ie_transaction_journal_2`.`BranchID` AS `BranchID`, `ie_transaction_journal_2`.`FDate` AS `FDate`, `ie_transaction_journal_2`.`LDate` AS `LDate`, `ie_transaction_journal_2`.`RptUser` AS `RptUser` FROM `ie_transaction_journal_2` GROUP BY `ie_transaction_journal_2`.`AccountID`, `ie_transaction_journal_2`.`RptUser` ;

-- --------------------------------------------------------

--
-- Structure for view `inharbor_pending`
--
DROP TABLE IF EXISTS `inharbor_pending`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `inharbor_pending`  AS SELECT `container_main`.`ConsignmentID` AS `ConsignmentID`, `container_main`.`CarrierID` AS `CarrierID`, `container_main`.`Rotation` AS `Rotation`, `container_main`.`ShipperID` AS `ShipperID`, `container_main`.`VesselName` AS `VesselName`, `container_main`.`VoyageNo` AS `VoyageNo`, `container_main`.`SealNo` AS `SealNo`, `container_main`.`ETA` AS `ETA`, `container_main`.`BL` AS `BL`, `container_main`.`ConsigneeID` AS `ConsigneeID`, `consignee_main`.`FullName` AS `ConsigneeName`, `container_main`.`ContainerNo` AS `ContainerNo`, `container_main`.`ContainerSize` AS `ContainerSize`, `container_main`.`ReceiptNo` AS `ReceiptNo`, `container_main`.`POIS` AS `POIS`, `container_main`.`DOIS` AS `DOIS`, `container_main`.`SOB` AS `SOB`, `container_main`.`POL_ID` AS `POL_ID`, `container_main`.`POD_ID` AS `POD_ID`, `container_main`.`ContWeight` AS `ContWeight`, `container_main`.`Charges` AS `Charges`, `container_main`.`AgentContact` AS `AgentContact`, `container_main`.`Destination` AS `Destination`, `container_main`.`Username` AS `Username`, `container_main`.`BranchID` AS `BranchID`, `container_main`.`Date` AS `Date`, `container_main`.`Time` AS `Time`, `container_main`.`Status` AS `Status` FROM (`container_main` left join `consignee_main` on(`container_main`.`ConsigneeID` = `consignee_main`.`ConsigneeID`)) WHERE `container_main`.`Status` = 1 AND `container_main`.`Ownership` = 1 ;

-- --------------------------------------------------------

--
-- Structure for view `inharbor_pending_1`
--
DROP TABLE IF EXISTS `inharbor_pending_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `inharbor_pending_1`  AS SELECT `inharbor_pending`.`ConsignmentID` AS `ConsignmentID`, `inharbor_pending`.`CarrierID` AS `CarrierID`, `inharbor_pending`.`ConsigneeID` AS `ConsigneeID`, `inharbor_pending`.`ConsigneeName` AS `ConsigneeName`, `inharbor_pending`.`Rotation` AS `Rotation`, `inharbor_pending`.`ShipperID` AS `ShipperID`, `inharbor_pending`.`VesselName` AS `VesselName`, `inharbor_pending`.`VoyageNo` AS `VoyageNo`, `inharbor_pending`.`SealNo` AS `SealNo`, `inharbor_pending`.`ETA` AS `ETA`, to_days(`inharbor_pending`.`ETA`) - to_days(curdate()) AS `ETADays`, `inharbor_pending`.`BL` AS `BL`, `inharbor_pending`.`ContainerNo` AS `ContainerNo`, `inharbor_pending`.`ContainerSize` AS `ContainerSize`, `inharbor_pending`.`ContWeight` AS `ContWeight`, `inharbor_pending`.`Charges` AS `Charges`, `inharbor_pending`.`AgentContact` AS `AgentContact`, `inharbor_pending`.`Destination` AS `Destination`, `inharbor_pending`.`Username` AS `Username`, `inharbor_pending`.`BranchID` AS `BranchID`, `inharbor_pending`.`Date` AS `Date`, `inharbor_pending`.`Time` AS `Time`, `inharbor_pending`.`Status` AS `Status` FROM `inharbor_pending` ;

-- --------------------------------------------------------

--
-- Structure for view `inst_branch_view`
--
DROP TABLE IF EXISTS `inst_branch_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `inst_branch_view`  AS SELECT `inst_branch`.`InstID` AS `InstID`, `inst_reg`.`InstName` AS `InstName`, `inst_reg`.`Website` AS `Website`, `inst_reg`.`Email` AS `Email`, `inst_branch`.`BranchID` AS `BranchID`, `inst_branch`.`BranchName` AS `BranchName`, `inst_branch`.`Address` AS `Address`, `inst_branch`.`TelNo` AS `TelNo`, `inst_branch`.`Location` AS `Location`, `inst_branch`.`Date` AS `Date`, `inst_branch`.`Time` AS `Time`, `inst_branch`.`Username` AS `Username` FROM (`inst_branch` join `inst_reg` on(`inst_branch`.`InstID` = `inst_reg`.`InstID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `journal_view`
--
DROP TABLE IF EXISTS `journal_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `journal_view`  AS SELECT `ledger_account`.`ControlID` AS `ControlID`, `ledger_account`.`CategoryID` AS `CategoryID`, `journal`.`AccountID` AS `AccountID`, `ledger_account`.`AccountName` AS `AccountName`, `journal`.`SubAccountID` AS `SubAccountID`, `journal`.`Mode` AS `Mode`, `journal`.`TType` AS `TType`, `journal`.`ReceiptNo` AS `ReceiptNo`, round(sum(`journal`.`Dr`),2) AS `Dr`, round(sum(`journal`.`Cr`),2) AS `Cr`, `journal`.`Description` AS `Description`, `journal`.`Date` AS `Date`, `journal`.`Time` AS `Time`, `journal`.`Username` AS `Username`, `journal`.`Authorizer` AS `Authorizer`, `journal`.`BranchID` AS `BranchID`, `journal`.`Status` AS `Status` FROM (`journal` join `ledger_account` on(`journal`.`AccountID` = `ledger_account`.`AccountNo`)) GROUP BY `journal`.`AccountID`, `journal`.`ReceiptNo` ;

-- --------------------------------------------------------

--
-- Structure for view `journal_view_0`
--
DROP TABLE IF EXISTS `journal_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `journal_view_0`  AS SELECT `journal_view`.`ControlID` AS `ControlID`, `journal_view`.`CategoryID` AS `CategoryID`, `journal_view`.`AccountID` AS `AccountID`, `journal_view`.`AccountName` AS `AccountName`, `journal_view`.`SubAccountID` AS `SubAccountID`, `ledger_account`.`AccountName` AS `SubAccountName`, `journal_view`.`Mode` AS `Mode`, `journal_view`.`TType` AS `TType`, `journal_view`.`ReceiptNo` AS `ReceiptNo`, `journal_view`.`Dr` AS `Dr`, `journal_view`.`Cr` AS `Cr`, `journal_view`.`Description` AS `Description`, `journal_view`.`Date` AS `Date`, `journal_view`.`Time` AS `Time`, `journal_view`.`Username` AS `Username`, `journal_view`.`Authorizer` AS `Authorizer`, `journal_view`.`BranchID` AS `BranchID`, `inst_branch`.`BranchName` AS `BranchName`, `journal_view`.`Status` AS `Status` FROM ((`journal_view` join `ledger_account` on(`journal_view`.`SubAccountID` = `ledger_account`.`AccountNo`)) join `inst_branch` on(`journal_view`.`BranchID` = `inst_branch`.`BranchID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `kaina_view`
--
DROP TABLE IF EXISTS `kaina_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `kaina_view`  AS SELECT `kaina`.`ID` AS `ID`, CASE WHEN `student_list`.`FullName` is null THEN `kaina`.`FullName` ELSE `student_list`.`FullName` END AS `FullName`, `kaina`.`Initial` AS `Initial`, `kaina`.`Password` AS `Password`, md5(`kaina`.`Password`) AS `NewPass`, `kaina`.`Nature` AS `Nature`, `kaina`.`Stats` AS `Stats`, `kaina`.`BranchID` AS `BranchID`, `inst_branch`.`Branch` AS `BranchName` FROM (((`kaina` left join `student_list` on(`kaina`.`ID` = `student_list`.`StudentID`)) left join `users` on(`kaina`.`ID` = `users`.`MemberID`)) left join `inst_branch` on(`kaina`.`BranchID` = `inst_branch`.`BranchID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `ledger_account_expenditure`
--
DROP TABLE IF EXISTS `ledger_account_expenditure`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ledger_account_expenditure`  AS SELECT `ledger_account`.`ControlID` AS `ControlID`, `ledger_account`.`CategoryID` AS `CategoryID`, `ledger_category`.`SubCategoryName` AS `SubCategoryName`, `ledger_account`.`Class` AS `Class`, CASE WHEN `ledger_account`.`Nature` = 'BL' THEN 'BILLABLE' ELSE 'NON-BILLABLE' END AS `Nature`, `ledger_account`.`Type` AS `Type`, `ledger_account`.`AccountNo` AS `AccountNo`, `ledger_account`.`AccountName` AS `AccountName`, `ledger_account`.`Date` AS `Date`, `ledger_account`.`Time` AS `Time`, `ledger_account`.`Status` AS `Status`, `ledger_account`.`Visible` AS `Visible`, `ledger_account`.`Username` AS `Username` FROM (`ledger_account` join `ledger_category` on(`ledger_account`.`CategoryID` = `ledger_category`.`SubCategoryID`)) WHERE `ledger_account`.`Type` = 'EXPENDITURE' ;

-- --------------------------------------------------------

--
-- Structure for view `ledger_account_gl`
--
DROP TABLE IF EXISTS `ledger_account_gl`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ledger_account_gl`  AS SELECT `ledger_account`.`ControlID` AS `ControlID`, `ledger_account`.`CategoryID` AS `CategoryID`, `ledger_account`.`Class` AS `Class`, `ledger_account`.`Nature` AS `Nature`, `ledger_account`.`Type` AS `Type`, `ledger_account`.`AccountNo` AS `AccountNo`, `ledger_account`.`AccountName` AS `AccountName`, `ledger_account`.`Date` AS `Date`, `ledger_account`.`Time` AS `Time`, `ledger_account`.`Status` AS `Status`, `ledger_account`.`Visible` AS `Visible`, `ledger_account`.`Username` AS `Username` FROM `ledger_account` WHERE `ledger_account`.`Type` = 'GL' ;

-- --------------------------------------------------------

--
-- Structure for view `ledger_account_income`
--
DROP TABLE IF EXISTS `ledger_account_income`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ledger_account_income`  AS SELECT `ledger_account`.`ControlID` AS `ControlID`, `ledger_account`.`CategoryID` AS `CategoryID`, `ledger_category`.`SubCategoryName` AS `SubCategoryName`, `ledger_account`.`Class` AS `Class`, CASE WHEN `ledger_account`.`Nature` = 'BL' THEN 'BILLABLE' ELSE 'NON-BILLABLE' END AS `Nature`, `ledger_account`.`Type` AS `Type`, `ledger_account`.`AccountNo` AS `AccountNo`, `ledger_account`.`AccountName` AS `AccountName`, `ledger_account`.`Date` AS `Date`, `ledger_account`.`Time` AS `Time`, `ledger_account`.`Status` AS `Status`, `ledger_account`.`Visible` AS `Visible`, `ledger_account`.`Username` AS `Username` FROM (`ledger_account` join `ledger_category` on(`ledger_account`.`CategoryID` = `ledger_category`.`SubCategoryID`)) WHERE `ledger_account`.`Type` = 'INCOME' ;

-- --------------------------------------------------------

--
-- Structure for view `ledger_account_view`
--
DROP TABLE IF EXISTS `ledger_account_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ledger_account_view`  AS SELECT `ledger_account`.`ControlID` AS `ControlID`, `ledger_control`.`ControlName` AS `ControlName`, `ledger_category`.`CategoryID` AS `CategoryID`, `ledger_category`.`CategoryName` AS `CategoryName`, `ledger_account`.`CategoryID` AS `SubCategoryID`, `ledger_category`.`SubCategoryName` AS `SubCategoryName`, `ledger_account`.`Class` AS `Class`, `ledger_account`.`Nature` AS `Nature`, `ledger_account`.`Type` AS `Type`, `ledger_account`.`AccountNo` AS `AccountNo`, `ledger_account`.`AccountName` AS `AccountName`, `ledger_account`.`Date` AS `Date`, `ledger_account`.`Time` AS `Time`, `ledger_account`.`Status` AS `Status`, `ledger_account`.`Visible` AS `Visible`, `ledger_account`.`Username` AS `Username` FROM ((`ledger_account` join `ledger_control` on(`ledger_account`.`ControlID` = `ledger_control`.`ControlID`)) left join `ledger_category` on(`ledger_account`.`CategoryID` = `ledger_category`.`SubCategoryID`)) WHERE `ledger_account`.`Status` = '1' ORDER BY `ledger_control`.`ControlName` ASC, `ledger_account`.`AccountName` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `manifestation_breakdown_cargo`
--
DROP TABLE IF EXISTS `manifestation_breakdown_cargo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `manifestation_breakdown_cargo`  AS SELECT `manifestation_breakdown`.`ConsignmentID` AS `ConsignmentID`, `container_main_view`.`ShipperID` AS `ShipperID`, `container_main_view`.`ShipperName` AS `ShipperName`, `container_main_view`.`VesselName` AS `VesselName`, `container_main_view`.`SealNo` AS `SealNo`, `container_main_view`.`ETA` AS `ETA`, `container_main_view`.`ContainerNo` AS `ContainerNo`, `container_main_view`.`ContainerSize` AS `ContainerSize`, `container_main_view`.`POL_ID` AS `POL_ID`, `container_main_view`.`POL_Name` AS `POL_Name`, `container_main_view`.`POD_ID` AS `POD_ID`, `container_main_view`.`POD_Name` AS `POD_Name`, `container_main_view`.`ContWeight` AS `ContWeight`, `manifestation_breakdown`.`MainBL` AS `MainBL`, `manifestation_breakdown`.`HouseBL` AS `HouseBL`, `manifestation_breakdown`.`ConsigneeID` AS `ConsigneeID`, `manifestation_breakdown`.`Consigenee2_ID` AS `Consigenee2_ID`, `consignee_main`.`FullName` AS `FN`, `manifestation_breakdown`.`Description` AS `Description`, `manifestation_breakdown`.`ItemType` AS `ItemType`, `manifestation_breakdown`.`VIN` AS `VIN`, `manifestation_breakdown`.`OtherInfo` AS `OtherInfo`, `manifestation_breakdown`.`Weight` AS `Weight`, `manifestation_breakdown`.`Package` AS `Package`, `manifestation_breakdown`.`Unit` AS `Unit`, `manifestation_breakdown`.`Username` AS `Username`, `manifestation_breakdown`.`Date` AS `Date`, `manifestation_breakdown`.`Time` AS `Time`, `manifestation_breakdown`.`Status` AS `Status` FROM ((`manifestation_breakdown` join `container_main_view` on(`manifestation_breakdown`.`ConsignmentID` = `container_main_view`.`ConsignmentID` and `manifestation_breakdown`.`ContainerNo` = `container_main_view`.`ContainerNo` and `manifestation_breakdown`.`MainBL` = `container_main_view`.`BL`)) join `consignee_main` on(`manifestation_breakdown`.`Consigenee2_ID` = `consignee_main`.`ConsigneeID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `manifestation_breakdown_cargo_view`
--
DROP TABLE IF EXISTS `manifestation_breakdown_cargo_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `manifestation_breakdown_cargo_view`  AS SELECT `manifestation_breakdown_cargo`.`ConsignmentID` AS `ConsignmentID`, `manifestation_breakdown_cargo`.`ShipperID` AS `ShipperID`, `manifestation_breakdown_cargo`.`ShipperName` AS `ShipperName`, `manifestation_breakdown_cargo`.`VesselName` AS `VesselName`, `manifestation_breakdown_cargo`.`ETA` AS `ETA`, `manifestation_breakdown_cargo`.`ContainerNo` AS `ContainerNo`, `manifestation_breakdown_cargo`.`SealNo` AS `SealNo`, `manifestation_breakdown_cargo`.`ContainerSize` AS `ContainerSize`, `manifestation_breakdown_cargo`.`POL_ID` AS `POL_ID`, `manifestation_breakdown_cargo`.`POL_Name` AS `POL_Name`, `manifestation_breakdown_cargo`.`POD_ID` AS `POD_ID`, `manifestation_breakdown_cargo`.`POD_Name` AS `POD_Name`, `manifestation_breakdown_cargo`.`ContWeight` AS `ContWeight`, `manifestation_breakdown_cargo`.`MainBL` AS `MainBL`, `manifestation_breakdown_cargo`.`HouseBL` AS `HouseBL`, `manifestation_breakdown_cargo`.`ConsigneeID` AS `ConsigneeID`, `consignee_main`.`FullName` AS `FullName`, `consignee_main`.`TelNo` AS `TelNo`, `consignee_main`.`Address1` AS `Address1`, `consignee_main`.`Address2` AS `Address2`, `consignee_main`.`Address3` AS `Address3`, `manifestation_breakdown_cargo`.`Consigenee2_ID` AS `Consigenee2_ID`, `manifestation_breakdown_cargo`.`Description` AS `Description`, `manifestation_breakdown_cargo`.`ItemType` AS `ItemType`, `manifestation_breakdown_cargo`.`VIN` AS `VIN`, `manifestation_breakdown_cargo`.`OtherInfo` AS `OtherInfo`, `manifestation_breakdown_cargo`.`Weight` AS `Weight`, `manifestation_breakdown_cargo`.`Package` AS `Package`, `manifestation_breakdown_cargo`.`Unit` AS `Unit`, `manifestation_breakdown_cargo`.`Username` AS `Username`, `manifestation_breakdown_cargo`.`Date` AS `Date`, `manifestation_breakdown_cargo`.`Time` AS `Time`, `manifestation_breakdown_cargo`.`Status` AS `Status` FROM (`manifestation_breakdown_cargo` join `consignee_main` on(`manifestation_breakdown_cargo`.`ConsigneeID` = `consignee_main`.`ConsigneeID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `manifestation_breakdown_hbl`
--
DROP TABLE IF EXISTS `manifestation_breakdown_hbl`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `manifestation_breakdown_hbl`  AS SELECT `manifestation_breakdown`.`ConsignmentID` AS `ConsignmentID`, `container_main`.`ShipperID` AS `ShipperID`, `manifestation_breakdown`.`MainBL` AS `MainBL`, `manifestation_breakdown`.`HouseBL` AS `HouseBL`, `manifestation_breakdown`.`ContainerNo` AS `ContainerNo`, `container_main`.`POIS` AS `POIS`, `container_main`.`DOIS` AS `DOIS`, `container_main`.`SOB` AS `SOB`, `manifestation_breakdown`.`ConsigneeID` AS `ConsigneeID`, `consignee_main`.`FullName` AS `FullName`, `manifestation_breakdown`.`Consigenee2_ID` AS `Consigenee2_ID`, `manifestation_breakdown`.`Description` AS `Description`, `manifestation_breakdown`.`Weight` AS `Weight`, `manifestation_breakdown`.`Package` AS `Package`, `manifestation_breakdown`.`Unit` AS `Unit`, `manifestation_breakdown`.`Username` AS `Username`, `manifestation_breakdown`.`Date` AS `Date`, `manifestation_breakdown`.`Time` AS `Time`, `manifestation_breakdown`.`Status` AS `Status` FROM ((`manifestation_breakdown` join `container_main` on(`manifestation_breakdown`.`ConsignmentID` = `container_main`.`ConsignmentID` and `manifestation_breakdown`.`MainBL` = `container_main`.`BL`)) join `consignee_main` on(`manifestation_breakdown`.`ConsigneeID` = `consignee_main`.`ConsigneeID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `manifestation_breakdown_hbl_0`
--
DROP TABLE IF EXISTS `manifestation_breakdown_hbl_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `manifestation_breakdown_hbl_0`  AS SELECT `manifestation_breakdown_hbl`.`ConsignmentID` AS `ConsignmentID`, `manifestation_breakdown_hbl`.`ShipperID` AS `ShipperID`, `manifestation_breakdown_hbl`.`MainBL` AS `MainBL`, `manifestation_breakdown_hbl`.`HouseBL` AS `HouseBL`, `manifestation_breakdown_hbl`.`ContainerNo` AS `ContainerNo`, `container_details`.`SealNo` AS `SealNo`, `manifestation_breakdown_hbl`.`POIS` AS `POIS`, `manifestation_breakdown_hbl`.`DOIS` AS `DOIS`, `manifestation_breakdown_hbl`.`SOB` AS `SOB`, `manifestation_breakdown_hbl`.`ConsigneeID` AS `ConsigneeID`, `manifestation_breakdown_hbl`.`FullName` AS `FullName`, `manifestation_breakdown_hbl`.`Consigenee2_ID` AS `Consigenee2_ID`, `manifestation_breakdown_hbl`.`Description` AS `Description`, `manifestation_breakdown_hbl`.`Weight` AS `Weight`, `manifestation_breakdown_hbl`.`Package` AS `Package`, `manifestation_breakdown_hbl`.`Unit` AS `Unit`, `manifestation_breakdown_hbl`.`Username` AS `Username`, `manifestation_breakdown_hbl`.`Date` AS `Date`, `manifestation_breakdown_hbl`.`Time` AS `Time`, `manifestation_breakdown_hbl`.`Status` AS `Status` FROM (`manifestation_breakdown_hbl` left join `container_details` on(`manifestation_breakdown_hbl`.`ConsignmentID` = `container_details`.`ConsignmentID` and `manifestation_breakdown_hbl`.`MainBL` = `container_details`.`BL` and `manifestation_breakdown_hbl`.`ContainerNo` = `container_details`.`ContainerNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `manifestation_breakdown_search`
--
DROP TABLE IF EXISTS `manifestation_breakdown_search`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `manifestation_breakdown_search`  AS SELECT `manifestation_breakdown`.`ConsignmentID` AS `ConsignmentID`, `manifestation_breakdown`.`MainBL` AS `MainBL`, round(sum(`manifestation_breakdown`.`Weight`),2) AS `TWeight`, `manifestation_breakdown`.`Username` AS `Username` FROM `manifestation_breakdown` GROUP BY `manifestation_breakdown`.`ConsignmentID`, `manifestation_breakdown`.`MainBL` ;

-- --------------------------------------------------------

--
-- Structure for view `manifestation_breakdown_view`
--
DROP TABLE IF EXISTS `manifestation_breakdown_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `manifestation_breakdown_view`  AS SELECT `manifestation_breakdown`.`ConsignmentID` AS `ConsignmentID`, `manifestation_breakdown`.`MainBL` AS `MainBL`, `manifestation_breakdown`.`ContainerNo` AS `ContainerNo`, `manifestation_breakdown`.`HouseBL` AS `HouseBL`, `manifestation_breakdown`.`ConsigneeID` AS `ConsigneeID`, `consignee_main`.`FullName` AS `FullName`, CASE WHEN `hbl_invoice_view_0`.`TFee` is null THEN 0 ELSE `hbl_invoice_view_0`.`TFee` END AS `TFee`, `manifestation_breakdown`.`Consigenee2_ID` AS `Consigenee2_ID`, `manifestation_breakdown`.`Description` AS `Description`, `manifestation_breakdown`.`Weight` AS `Weight`, `manifestation_breakdown`.`Package` AS `Package`, `manifestation_breakdown`.`Unit` AS `Unit`, `manifestation_breakdown`.`Username` AS `Username`, `container_main`.`Date` AS `TDate`, `manifestation_breakdown`.`Date` AS `Date`, `manifestation_breakdown`.`Time` AS `Time`, `manifestation_breakdown`.`Status` AS `Status` FROM (((`manifestation_breakdown` join `consignee_main` on(`manifestation_breakdown`.`ConsigneeID` = `consignee_main`.`ConsigneeID`)) join `container_main` on(`manifestation_breakdown`.`ConsignmentID` = `container_main`.`ConsignmentID`)) left join `hbl_invoice_view_0` on(`manifestation_breakdown`.`ConsignmentID` = `hbl_invoice_view_0`.`ConsignmentID` and `manifestation_breakdown`.`MainBL` = `hbl_invoice_view_0`.`MainBL` and `manifestation_breakdown`.`HouseBL` = `hbl_invoice_view_0`.`HouseBL` and `manifestation_breakdown`.`ConsigneeID` = `hbl_invoice_view_0`.`ConsigneeID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `manifestation_breakdown_view_0`
--
DROP TABLE IF EXISTS `manifestation_breakdown_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `manifestation_breakdown_view_0`  AS SELECT `manifestation_breakdown_view`.`ConsignmentID` AS `ConsignmentID`, `manifestation_breakdown_view`.`MainBL` AS `MainBL`, `manifestation_breakdown_view`.`ContainerNo` AS `ContainerNo`, `manifestation_breakdown_view`.`HouseBL` AS `HouseBL`, `manifestation_breakdown_view`.`ConsigneeID` AS `ConsigneeID`, `manifestation_breakdown_view`.`FullName` AS `FullName`, `manifestation_breakdown_view`.`Consigenee2_ID` AS `Consigenee2_ID`, `manifestation_breakdown_view`.`Description` AS `Description`, `manifestation_breakdown_view`.`Weight` AS `Weight`, `manifestation_breakdown_view`.`Package` AS `Package`, `manifestation_breakdown_view`.`Unit` AS `Unit`, `manifestation_breakdown_view`.`TFee` AS `TFee`, `manifestation_breakdown_view`.`Username` AS `Username`, `manifestation_breakdown_view`.`TDate` AS `TDate`, `manifestation_breakdown_view`.`Date` AS `Date`, `manifestation_breakdown_view`.`Time` AS `Time`, `manifestation_breakdown_view`.`Status` AS `Status` FROM `manifestation_breakdown_view` WHERE `manifestation_breakdown_view`.`TFee` = 0 ;

-- --------------------------------------------------------

--
-- Structure for view `manifest_bl_tracking`
--
DROP TABLE IF EXISTS `manifest_bl_tracking`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `manifest_bl_tracking`  AS SELECT DISTINCT `manifestation_breakdown`.`MainBL` AS `MainBL`, `manifestation_breakdown`.`ConsignmentID` AS `ConsignmentID` FROM `manifestation_breakdown` WHERE 1 ;

-- --------------------------------------------------------

--
-- Structure for view `manifest_bl_tracking_1`
--
DROP TABLE IF EXISTS `manifest_bl_tracking_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `manifest_bl_tracking_1`  AS SELECT `manifest_bl_tracking`.`MainBL` AS `MainBL`, `manifest_bl_tracking`.`ConsignmentID` AS `ConsignmentID`, `container_main`.`ETA` AS `ETA` FROM (`manifest_bl_tracking` join `container_main` on(`manifest_bl_tracking`.`ConsignmentID` = `container_main`.`ConsignmentID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `map_admission_fee_view`
--
DROP TABLE IF EXISTS `map_admission_fee_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `map_admission_fee_view`  AS SELECT `class_main_view`.`ClassID` AS `CourseID`, `class_main_view`.`ClassName` AS `CourseName`, `map_admission_fee`.`AccountID` AS `AccountID`, `ledger_account`.`AccountName` AS `AccountName`, round(`map_admission_fee`.`Amount`,2) AS `Amount`, `map_admission_fee`.`Date` AS `Date`, `map_admission_fee`.`Time` AS `Time`, `map_admission_fee`.`Username` AS `Username` FROM ((`map_admission_fee` join `class_main_view` on(`map_admission_fee`.`ClassID` = `class_main_view`.`ClassID`)) join `ledger_account` on(`map_admission_fee`.`AccountID` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `map_school_fee_view`
--
DROP TABLE IF EXISTS `map_school_fee_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `map_school_fee_view`  AS SELECT `map_school_fee`.`SubClassID` AS `SubClassID`, `sub_class_main`.`SubClassName` AS `SubClassName`, `map_school_fee`.`AccountID` AS `AccountID`, `ledger_account`.`AccountName` AS `AccountName`, `map_school_fee`.`Amount` AS `Amount`, `map_school_fee`.`Date` AS `Date`, `map_school_fee`.`Time` AS `Time`, `map_school_fee`.`Username` AS `Username` FROM ((`map_school_fee` join `sub_class_main` on(`map_school_fee`.`SubClassID` = `sub_class_main`.`SubClassID`)) join `ledger_account` on(`map_school_fee`.`AccountID` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `member_temp_selection_view`
--
DROP TABLE IF EXISTS `member_temp_selection_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `member_temp_selection_view`  AS SELECT `member_temp_selection`.`MemberID` AS `MemberID`, `student_list`.`FullName` AS `MemberName`, `student_list`.`ClassrmName` AS `ClassrmName`, `users`.`Picture` AS `Picture`, `member_temp_selection`.`Username` AS `Username`, `member_temp_selection`.`Status` AS `Status` FROM ((`member_temp_selection` join `student_list` on(`member_temp_selection`.`MemberID` = `student_list`.`StudentID`)) join `users` on(`member_temp_selection`.`MemberID` = `users`.`MemberID`)) ORDER BY `student_list`.`FullName` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `my_post_reply_view`
--
DROP TABLE IF EXISTS `my_post_reply_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `my_post_reply_view`  AS SELECT `my_post_reply`.`postid` AS `postid`, `my_post_reply`.`MemberID` AS `MemberID`, `student_list`.`FullName` AS `FullName`, `my_post_reply`.`reply` AS `reply`, `my_post_reply`.`Date` AS `Date`, `my_post_reply`.`Time` AS `Time`, floor(time_to_sec(timediff(current_timestamp(),`my_post_reply`.`Time`)) / 60) AS `MinsAgo`, timediff(current_timestamp(),`my_post_reply`.`Time`) AS `Ago`, current_timestamp() AS `Now`, `my_post_reply`.`Status` AS `Status` FROM (`my_post_reply` join `student_list` on(`my_post_reply`.`MemberID` = `student_list`.`StudentID`)) WHERE `my_post_reply`.`Status` <> '0' ORDER BY `my_post_reply`.`Time` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `my_post_reply_view_0`
--
DROP TABLE IF EXISTS `my_post_reply_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `my_post_reply_view_0`  AS SELECT `my_post_reply_view`.`postid` AS `postid`, `my_post_reply_view`.`MemberID` AS `MemberID`, `my_post_reply_view`.`FullName` AS `FullName`, `my_post_reply_view`.`reply` AS `reply`, `my_post_reply_view`.`Date` AS `Date`, `my_post_reply_view`.`Time` AS `Time`, CASE WHEN `my_post_reply_view`.`MinsAgo` between -1 and 0 THEN concat('just now') WHEN `my_post_reply_view`.`MinsAgo` < 60 THEN concat(`my_post_reply_view`.`MinsAgo`,'','m') WHEN `my_post_reply_view`.`MinsAgo` <= 1440 THEN concat(floor(`my_post_reply_view`.`MinsAgo` / 60),'','h') WHEN `my_post_reply_view`.`MinsAgo` <= 7200 THEN concat(floor(`my_post_reply_view`.`MinsAgo` / 1440),'','d') WHEN `my_post_reply_view`.`MinsAgo` > 7200 THEN concat(floor(`my_post_reply_view`.`MinsAgo` / 7200),'','wk') ELSE 'just now' END AS `MinsAgo`, `my_post_reply_view`.`Ago` AS `Ago`, `my_post_reply_view`.`Now` AS `Now`, `my_post_reply_view`.`Status` AS `Status` FROM `my_post_reply_view` ORDER BY `my_post_reply_view`.`Time` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `my_post_viewers_view`
--
DROP TABLE IF EXISTS `my_post_viewers_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `my_post_viewers_view`  AS SELECT `my_post_viewers`.`postid` AS `postid`, `my_post_viewers`.`MemberID` AS `MemberID`, `my_post`.`content` AS `content`, `my_post_viewers`.`Like` AS `Like`, `my_post_viewers`.`Date` AS `Date`, `my_post_viewers`.`Time` AS `Time`, `my_post_viewers`.`Status` AS `Status`, `my_post`.`Status` AS `PostStats`, `my_post`.`Username` AS `Username` FROM (`my_post_viewers` join `my_post` on(`my_post_viewers`.`postid` = `my_post`.`postid`)) WHERE `my_post`.`Status` <> '0' ;

-- --------------------------------------------------------

--
-- Structure for view `my_post_viewers_view_0`
--
DROP TABLE IF EXISTS `my_post_viewers_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `my_post_viewers_view_0`  AS SELECT `my_post_viewers_view`.`postid` AS `postid`, `my_post_viewers_view`.`MemberID` AS `MemberID`, `my_post_viewers_view`.`content` AS `content`, `my_post_viewers_view`.`Like` AS `Like`, `users`.`Picture` AS `Picture`, `my_post_viewers_view`.`Date` AS `Date`, `my_post_viewers_view`.`Time` AS `Time`, `my_post_viewers_view`.`Status` AS `Status`, `my_post_viewers_view`.`Username` AS `Username`, `student_list`.`FullName` AS `FullName` FROM ((`my_post_viewers_view` join `student_list` on(`my_post_viewers_view`.`Username` = `student_list`.`StudentID`)) join `users` on(`my_post_viewers_view`.`Username` = `users`.`MemberID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `notifications_view`
--
DROP TABLE IF EXISTS `notifications_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `notifications_view`  AS SELECT `notifications`.`MakerID` AS `MakerID`, `kaina`.`FullName` AS `FullName`, `notifications`.`Task` AS `Task`, `notifications`.`Status` AS `Status`, `notifications`.`Username` AS `Username`, `notifications`.`KeyType` AS `KeyType`, `notifications`.`Date` AS `Date`, `notifications`.`Time` AS `Time` FROM (`notifications` join `kaina` on(`notifications`.`MakerID` = `kaina`.`ID`)) WHERE `notifications`.`Status` <> '0' ;

-- --------------------------------------------------------

--
-- Structure for view `notifications_view_0`
--
DROP TABLE IF EXISTS `notifications_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `notifications_view_0`  AS SELECT `notifications_view`.`MakerID` AS `MakerID`, `notifications_view`.`FullName` AS `FullName`, `notifications_view`.`Task` AS `Task`, `notifications_view`.`Status` AS `Status`, `notifications_view`.`Username` AS `Username`, `notifications_view`.`KeyType` AS `KeyType`, `kaina`.`FullName` AS `UserFName`, `notifications_view`.`Date` AS `Date`, `notifications_view`.`Time` AS `Time` FROM (`notifications_view` join `kaina` on(`notifications_view`.`Username` = `kaina`.`ID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `pnl_transaction_balances`
--
DROP TABLE IF EXISTS `pnl_transaction_balances`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `pnl_transaction_balances`  AS SELECT `pnl_transaction`.`AccountID` AS `AccountID`, `pnl_transaction`.`Stamp` AS `Stamp`, `pnl_transaction`.`Mode` AS `Mode`, `pnl_transaction`.`ReceiptNo` AS `ReceiptNo`, `pnl_transaction`.`Description` AS `Description`, `pnl_transaction`.`Dr` AS `Dr`, `pnl_transaction`.`Cr` AS `Cr`, `pnl_transaction`.`Date` AS `Date`, `pnl_transaction`.`Time` AS `Time`, `pnl_transaction`.`BranchID` AS `BranchID`, `pnl_transaction`.`Username` AS `Username` FROM `pnl_transaction` ;

-- --------------------------------------------------------

--
-- Structure for view `pnl_transaction_balances_0`
--
DROP TABLE IF EXISTS `pnl_transaction_balances_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `pnl_transaction_balances_0`  AS SELECT `pnl_transaction_balances`.`AccountID` AS `AccountID`, `ledger_account`.`AccountName` AS `AccountName`, `ledger_account`.`Type` AS `Type`, round(sum(`pnl_transaction_balances`.`Dr`),2) AS `TDr`, round(sum(`pnl_transaction_balances`.`Cr`),2) AS `TCr`, `pnl_transaction_balances`.`BranchID` AS `BranchID` FROM (`pnl_transaction_balances` join `ledger_account` on(`pnl_transaction_balances`.`AccountID` = `ledger_account`.`AccountNo`)) GROUP BY `pnl_transaction_balances`.`AccountID`, `pnl_transaction_balances`.`BranchID` ;

-- --------------------------------------------------------

--
-- Structure for view `pnl_transaction_balances_1`
--
DROP TABLE IF EXISTS `pnl_transaction_balances_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `pnl_transaction_balances_1`  AS SELECT `pnl_transaction_balances_0`.`AccountID` AS `AccountID`, `pnl_transaction_balances_0`.`AccountName` AS `AccountName`, `pnl_transaction_balances_0`.`Type` AS `Type`, `pnl_transaction_balances_0`.`TDr` AS `TDr`, `pnl_transaction_balances_0`.`TCr` AS `TCr`, round(`pnl_transaction_balances_0`.`TCr` - `pnl_transaction_balances_0`.`TDr`,2) AS `Balance`, `pnl_transaction_balances_0`.`BranchID` AS `BranchID` FROM `pnl_transaction_balances_0` ;

-- --------------------------------------------------------

--
-- Structure for view `pnl_transaction_general`
--
DROP TABLE IF EXISTS `pnl_transaction_general`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `pnl_transaction_general`  AS SELECT `pnl_transaction`.`AccountID` AS `AccountID`, `ledger_account`.`AccountName` AS `AccountName`, `pnl_transaction`.`Stamp` AS `Stamp`, `pnl_transaction`.`Mode` AS `Mode`, `pnl_transaction`.`MainBL` AS `MainBL`, `pnl_transaction`.`HouseBL` AS `HouseBL`, `pnl_transaction`.`ReceiptNo` AS `ReceiptNo`, `pnl_transaction`.`Description` AS `Description`, `pnl_transaction`.`Dr` AS `Dr`, `pnl_transaction`.`Cr` AS `Cr`, `pnl_transaction`.`Date` AS `Date`, `pnl_transaction`.`Time` AS `Time`, `pnl_transaction`.`BranchID` AS `BranchID`, `pnl_transaction`.`Username` AS `Username`, `pnl_transaction`.`Status` AS `Status` FROM (`pnl_transaction` left join `ledger_account` on(`pnl_transaction`.`AccountID` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `pnl_transaction_recon`
--
DROP TABLE IF EXISTS `pnl_transaction_recon`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `pnl_transaction_recon`  AS SELECT `container_main`.`ConsignmentID` AS `ConsignmentID`, `container_main`.`CarrierID` AS `CarrierID`, `container_main`.`BL` AS `BL`, `container_main`.`ContainerNo` AS `ContainerNo`, `container_main`.`ContainerSize` AS `ContainerSize`, `container_main`.`ReceiptNo` AS `ReceiptNo`, `pnl_transaction`.`ReceiptNo` AS `RecNo`, `container_main`.`ContWeight` AS `ContWeight`, `container_main`.`Charges` AS `Charges` FROM (`container_main` join `pnl_transaction` on(`container_main`.`BL` = `pnl_transaction`.`MainBL`)) ;

-- --------------------------------------------------------

--
-- Structure for view `post_likes`
--
DROP TABLE IF EXISTS `post_likes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `post_likes`  AS SELECT `my_post_viewers`.`postid` AS `postid`, `my_post_viewers`.`MemberID` AS `MemberID`, count(`my_post_viewers`.`Like`) AS `LikeCount`, `my_post_viewers`.`Date` AS `Date` FROM `my_post_viewers` WHERE `my_post_viewers`.`Status` <> '0' AND `my_post_viewers`.`Like` = '1' GROUP BY `my_post_viewers`.`postid` ;

-- --------------------------------------------------------

--
-- Structure for view `receipt_momo`
--
DROP TABLE IF EXISTS `receipt_momo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `receipt_momo`  AS SELECT DISTINCT `receipt_main`.`Username` AS `Username`, max(`receipt_main`.`ReceiptNo`) AS `RcptNo`, max(`receipt_main`.`ID`) AS `ID`, max(`receipt_main`.`Date`) AS `Date` FROM `receipt_main` GROUP BY `receipt_main`.`Username` ;

-- --------------------------------------------------------

--
-- Structure for view `receipt_momo_0`
--
DROP TABLE IF EXISTS `receipt_momo_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `receipt_momo_0`  AS SELECT max(`receipt_main`.`ID`) AS `ID`, max(`receipt_main`.`ReceiptNo`) AS `max(receipt_main.ReceiptNo)`, max(`receipt_main`.`Date`) AS `max(receipt_main.Date)`, `receipt_momo`.`Username` AS `Username` FROM (`receipt_main` join `receipt_momo` on(`receipt_momo`.`Username` = `receipt_main`.`Username`)) ;

-- --------------------------------------------------------

--
-- Structure for view `service_charge_pnl`
--
DROP TABLE IF EXISTS `service_charge_pnl`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `service_charge_pnl`  AS SELECT `service_charge_main`.`ServiceID` AS `ServiceID`, `service_charge_main`.`BL` AS `BL`, `service_charge_main`.`DeclarationID` AS `DeclarationID`, `service_charge_main`.`DeclarationNo` AS `DeclarationNo`, `service_charge_main`.`ConsigneeID` AS `ConsigneeID`, `service_charge_main`.`ConsigneeName` AS `ConsigneeName`, `service_charge_main`.`Description` AS `Description`, `service_charge_main`.`Amount` AS `Amount`, `pnl_transaction`.`Cr` AS `Cr`, `service_charge_main`.`ReceiptNo` AS `ReceiptNo`, `service_charge_main`.`Date` AS `Date`, `service_charge_main`.`Time` AS `Time`, `service_charge_main`.`Username` AS `Username`, `service_charge_main`.`BranchID` AS `BranchID`, `service_charge_main`.`Status` AS `Status` FROM (`service_charge_main` left join `pnl_transaction` on(`service_charge_main`.`ReceiptNo` = `pnl_transaction`.`ReceiptNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `setcontainerno`
--
DROP TABLE IF EXISTS `setcontainerno`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `setcontainerno`  AS SELECT `manifestation_breakdown`.`ConsignmentID` AS `ConsignmentID`, `manifestation_breakdown`.`MainBL` AS `MainBL`, `manifestation_breakdown`.`ContainerNo` AS `ContainerNo`, `container_main_view`.`ContainerNo` AS `CntNo`, `manifestation_breakdown`.`HouseBL` AS `HouseBL`, `manifestation_breakdown`.`ConsigneeID` AS `ConsigneeID`, `manifestation_breakdown`.`Consigenee2_ID` AS `Consigenee2_ID`, `manifestation_breakdown`.`Description` AS `Description`, `manifestation_breakdown`.`ItemType` AS `ItemType`, `manifestation_breakdown`.`VIN` AS `VIN`, `manifestation_breakdown`.`OtherInfo` AS `OtherInfo`, `manifestation_breakdown`.`Weight` AS `Weight`, `manifestation_breakdown`.`Package` AS `Package`, `manifestation_breakdown`.`Unit` AS `Unit`, `manifestation_breakdown`.`Username` AS `Username`, `manifestation_breakdown`.`Date` AS `Date`, `manifestation_breakdown`.`Time` AS `Time`, `manifestation_breakdown`.`Status` AS `Status` FROM (`manifestation_breakdown` join `container_main_view` on(`manifestation_breakdown`.`ConsignmentID` = `container_main_view`.`ConsignmentID`)) WHERE `manifestation_breakdown`.`ContainerNo` = '' ;

-- --------------------------------------------------------

--
-- Structure for view `set_accounts_view`
--
DROP TABLE IF EXISTS `set_accounts_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `set_accounts_view`  AS SELECT `set_accounts`.`PNL` AS `PNLID`, `ledger_account`.`AccountName` AS `PNLName` FROM (`set_accounts` join `ledger_account` on(`set_accounts`.`PNL` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `staff_class_subj_mapp_view`
--
DROP TABLE IF EXISTS `staff_class_subj_mapp_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `staff_class_subj_mapp_view`  AS SELECT `staff_class_subj_mapp`.`StaffID` AS `StaffID`, `staff_main`.`FullName` AS `StaffName`, `staff_class_subj_mapp`.`SubClassID` AS `SubClassID`, `sub_class_main`.`SubClassName` AS `SubClassName`, `staff_class_subj_mapp`.`SubjectID` AS `SubjectID`, `subject_main`.`SubjectName` AS `SubjectName`, `staff_class_subj_mapp`.`Date` AS `Date`, `staff_class_subj_mapp`.`Time` AS `Time`, `staff_class_subj_mapp`.`Username` AS `Username`, `staff_class_subj_mapp`.`Status` AS `Status` FROM (((`staff_class_subj_mapp` join `staff_main` on(`staff_class_subj_mapp`.`StaffID` = `staff_main`.`StaffID`)) join `sub_class_main` on(`staff_class_subj_mapp`.`SubClassID` = `sub_class_main`.`SubClassID`)) join `subject_main` on(`staff_class_subj_mapp`.`SubjectID` = `subject_main`.`SubjectID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `staff_main_view`
--
DROP TABLE IF EXISTS `staff_main_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `staff_main_view`  AS SELECT `staff_main`.`Code` AS `Code`, `staff_main`.`StaffID` AS `StaffID`, `staff_main`.`FullName` AS `FullName`, `staff_main`.`DOB` AS `DOB`, `staff_main`.`Gender` AS `Gender`, `staff_main`.`TelNo` AS `TelNo`, `staff_main`.`Address` AS `Address`, `staff_main`.`Appointment_Date` AS `Appointment_Date`, `staff_main`.`RegdNo` AS `RegdNo`, `staff_main`.`SSNIT` AS `SSNIT`, `staff_main`.`AcademicQlf` AS `AcademicQlf`, `staff_main`.`ProfQlf` AS `ProfQlf`, `staff_main`.`Rank` AS `Rank`, `staff_main`.`PromoDate` AS `PromoDate`, `staff_main`.`PostedDate` AS `PostedDate`, `staff_main`.`SchlDate` AS `SchlDate`, `staff_main`.`ClassTaught` AS `ClassTaught`, `staff_main`.`Type` AS `Type`, `staff_main`.`Date` AS `Date`, `staff_main`.`Time` AS `Time`, `staff_main`.`Username` AS `Username`, `staff_main`.`Status` AS `Status`, `users`.`Picture` AS `Picture` FROM (`staff_main` left join `users` on(`staff_main`.`StaffID` = `users`.`MemberID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `staff_main_view_0`
--
DROP TABLE IF EXISTS `staff_main_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `staff_main_view_0`  AS SELECT `staff_main_view`.`Code` AS `Code`, `staff_main_view`.`StaffID` AS `StaffID`, `staff_main_view`.`FullName` AS `FullName`, `staff_main_view`.`DOB` AS `DOB`, `staff_main_view`.`Gender` AS `Gender`, `staff_main_view`.`TelNo` AS `TelNo`, `staff_main_view`.`Address` AS `Address`, `staff_main_view`.`Appointment_Date` AS `Appointment_Date`, `staff_main_view`.`RegdNo` AS `RegdNo`, `staff_main_view`.`SSNIT` AS `SSNIT`, `staff_main_view`.`AcademicQlf` AS `AcademicQlf`, `staff_main_view`.`ProfQlf` AS `ProfQlf`, `staff_main_view`.`Rank` AS `Rank`, `staff_main_view`.`PromoDate` AS `PromoDate`, `staff_main_view`.`PostedDate` AS `PostedDate`, `staff_main_view`.`SchlDate` AS `SchlDate`, `staff_main_view`.`ClassTaught` AS `ClassTaught`, `staff_main_view`.`Type` AS `Type`, `staff_main_view`.`Date` AS `Date`, `staff_main_view`.`Time` AS `Time`, `staff_main_view`.`Username` AS `Username`, `staff_main_view`.`Status` AS `Status`, CASE WHEN `staff_main_view`.`Picture` is null THEN 'generic.jpg' ELSE `staff_main_view`.`Picture` END AS `Picture` FROM `staff_main_view` ;

-- --------------------------------------------------------

--
-- Structure for view `student_cont_assesment_0`
--
DROP TABLE IF EXISTS `student_cont_assesment_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_cont_assesment_0`  AS SELECT `student_test_score`.`CouponNo` AS `CouponNo`, `student_test_score`.`StudentID` AS `StudentID`, `student_test_score`.`SubClassID` AS `SubClassID`, `student_test_score`.`TestType` AS `TestType`, `student_test_score`.`TestID` AS `TestID`, `test_setup_view`.`Type` AS `Type`, `test_type`.`TypeID` AS `TypeID`, `test_setup_view`.`TestName` AS `TestName`, `test_setup_view`.`MaxScore` AS `MaxScore`, `student_test_score`.`SubjectID` AS `SubjectID`, `student_test_score`.`Score` AS `Score`, `student_test_score`.`Username` AS `Username`, `student_test_score`.`Date` AS `Date`, `student_test_score`.`Time` AS `Time`, `student_test_score`.`Status` AS `Status` FROM ((`student_test_score` join `test_setup_view` on(`student_test_score`.`TestID` = `test_setup_view`.`TestID`)) join `test_type` on(`test_setup_view`.`Type` = `test_type`.`TypeName`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_cont_assesment_1`
--
DROP TABLE IF EXISTS `student_cont_assesment_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_cont_assesment_1`  AS SELECT `student_cont_assesment_0`.`CouponNo` AS `CouponNo`, `student_cont_assesment_0`.`StudentID` AS `StudentID`, `student_cont_assesment_0`.`SubClassID` AS `SubClassID`, `student_cont_assesment_0`.`TypeID` AS `TypeID`, `student_cont_assesment_0`.`Type` AS `TypeName`, `student_cont_assesment_0`.`TestID` AS `TestID`, `student_cont_assesment_0`.`TestName` AS `TestName`, `student_cont_assesment_0`.`MaxScore` AS `MaxScore`, `student_cont_assesment_0`.`SubjectID` AS `SubjectID`, `student_cont_assesment_0`.`Score` AS `Score`, `student_cont_assesment_0`.`Username` AS `Username`, `student_cont_assesment_0`.`Date` AS `Date`, `student_cont_assesment_0`.`Time` AS `Time`, `student_cont_assesment_0`.`Status` AS `Status` FROM `student_cont_assesment_0` ;

-- --------------------------------------------------------

--
-- Structure for view `student_cont_assesment_class_test`
--
DROP TABLE IF EXISTS `student_cont_assesment_class_test`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_cont_assesment_class_test`  AS SELECT `student_cont_assesment_1`.`CouponNo` AS `CouponNo`, `student_cont_assesment_1`.`StudentID` AS `StudentID`, `student_cont_assesment_1`.`SubClassID` AS `SubClassID`, `student_cont_assesment_1`.`TypeID` AS `TypeID`, `student_cont_assesment_1`.`TypeName` AS `TypeName`, `student_cont_assesment_1`.`TestID` AS `TestID`, `student_cont_assesment_1`.`TestName` AS `TestName`, `student_cont_assesment_1`.`MaxScore` AS `MaxScore`, `student_cont_assesment_1`.`SubjectID` AS `SubjectID`, `student_cont_assesment_1`.`Score` AS `Score`, `student_cont_assesment_1`.`Username` AS `Username`, `student_cont_assesment_1`.`Date` AS `Date`, `student_cont_assesment_1`.`Time` AS `Time`, `student_cont_assesment_1`.`Status` AS `Status` FROM `student_cont_assesment_1` WHERE `student_cont_assesment_1`.`TypeID` = '1' OR `student_cont_assesment_1`.`TypeID` = '0' ;

-- --------------------------------------------------------

--
-- Structure for view `student_cont_assesment_main`
--
DROP TABLE IF EXISTS `student_cont_assesment_main`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_cont_assesment_main`  AS SELECT `student_cont_assesment_1`.`CouponNo` AS `CouponNo`, `academic_coupon`.`Title` AS `Title`, `student_cont_assesment_1`.`StudentID` AS `StudentID`, `student_main_view`.`FullName` AS `FullName`, `student_cont_assesment_1`.`SubClassID` AS `SubClassID`, `sub_class_main_view`.`SubClassName` AS `SubClassName`, `student_cont_assesment_1`.`TypeID` AS `TypeID`, `student_cont_assesment_1`.`TypeName` AS `TypeName`, `student_cont_assesment_1`.`TestID` AS `TestID`, `student_cont_assesment_1`.`TestName` AS `TestName`, `student_cont_assesment_1`.`MaxScore` AS `MaxScore`, `student_cont_assesment_1`.`SubjectID` AS `SubjectID`, `subject_main_view`.`SubjectName` AS `SubjectName`, `student_cont_assesment_1`.`Score` AS `Score`, `student_cont_assesment_1`.`Username` AS `Username`, `student_cont_assesment_1`.`Date` AS `Date`, `student_cont_assesment_1`.`Time` AS `Time`, `student_cont_assesment_1`.`Status` AS `Status` FROM ((((`student_cont_assesment_1` join `academic_coupon` on(`student_cont_assesment_1`.`CouponNo` = `academic_coupon`.`CouponNo`)) join `student_main_view` on(`student_cont_assesment_1`.`StudentID` = `student_main_view`.`StudentID`)) join `sub_class_main_view` on(`student_cont_assesment_1`.`SubClassID` = `sub_class_main_view`.`SubClassID`)) join `subject_main_view` on(`student_cont_assesment_1`.`SubjectID` = `subject_main_view`.`SubjectID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_current_class`
--
DROP TABLE IF EXISTS `student_current_class`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_current_class`  AS SELECT `student_promo`.`StudentID` AS `StudentID`, `student_main_view`.`FullName` AS `FullName`, `student_promo`.`PreviousClass` AS `PreviousClass`, `student_promo`.`PromoClass` AS `SubCurrentClassID`, `sub_class_main`.`SubClassName` AS `SubCurrentClassName`, `student_promo`.`Date` AS `Date`, `student_promo`.`Status` AS `Status` FROM ((`student_promo` join `sub_class_main` on(`student_promo`.`PromoClass` = `sub_class_main`.`SubClassID`)) join `student_main_view` on(`student_promo`.`StudentID` = `student_main_view`.`StudentID`)) WHERE `student_promo`.`Status` = '1' ;

-- --------------------------------------------------------

--
-- Structure for view `student_fee_outstading_view`
--
DROP TABLE IF EXISTS `student_fee_outstading_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_fee_outstading_view`  AS SELECT `student_fee`.`StudentID` AS `StudentID`, `student_fee`.`SubClassID` AS `SubClassID`, `student_fee`.`CouponID` AS `CouponID`, `student_fee`.`AccountNo` AS `AccountNo`, `student_fee`.`Stamp` AS `Stamp`, `student_fee`.`Description` AS `Description`, `student_fee`.`ReceiptNo` AS `ReceiptNo`, round(sum(`student_fee`.`Dr`),2) AS `TDr`, round(sum(`student_fee`.`Cr`),2) AS `TCr`, `student_fee`.`Date` AS `Date`, `student_fee`.`Time` AS `Time`, `student_fee`.`Username` AS `Username`, `student_fee`.`Status` AS `Status` FROM `student_fee` WHERE `student_fee`.`Status` = 1 GROUP BY `student_fee`.`StudentID`, `student_fee`.`SubClassID`, `student_fee`.`CouponID`, `student_fee`.`AccountNo` ;

-- --------------------------------------------------------

--
-- Structure for view `student_fee_outstading_view_0`
--
DROP TABLE IF EXISTS `student_fee_outstading_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_fee_outstading_view_0`  AS SELECT `student_fee_outstading_view`.`StudentID` AS `StudentID`, `consignee_main`.`FullName` AS `FullName`, `consignee_main`.`TelNo` AS `TelNo`, `student_fee_outstading_view`.`SubClassID` AS `SubClassID`, `student_fee_outstading_view`.`CouponID` AS `CouponID`, `student_fee_outstading_view`.`AccountNo` AS `AccountNo`, `student_fee_outstading_view`.`Stamp` AS `Stamp`, `student_fee_outstading_view`.`Description` AS `Description`, `student_fee_outstading_view`.`ReceiptNo` AS `ReceiptNo`, round(sum(`student_fee_outstading_view`.`TDr`),2) AS `TDr`, round(sum(`student_fee_outstading_view`.`TCr`),2) AS `TCr`, round(sum(`student_fee_outstading_view`.`TDr`) - sum(`student_fee_outstading_view`.`TCr`),2) AS `Balance`, `student_fee_outstading_view`.`Date` AS `Date`, `student_fee_outstading_view`.`Time` AS `Time`, `student_fee_outstading_view`.`Username` AS `Username`, `student_fee_outstading_view`.`Status` AS `Status` FROM (`student_fee_outstading_view` join `consignee_main` on(`student_fee_outstading_view`.`StudentID` = `consignee_main`.`ConsigneeID`)) GROUP BY `student_fee_outstading_view`.`StudentID`, `student_fee_outstading_view`.`SubClassID`, `student_fee_outstading_view`.`CouponID` ;

-- --------------------------------------------------------

--
-- Structure for view `student_fee_outstading_view_1`
--
DROP TABLE IF EXISTS `student_fee_outstading_view_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_fee_outstading_view_1`  AS SELECT `student_fee_outstading_view_0`.`StudentID` AS `StudentID`, `consignee_main`.`FullName` AS `FullName`, `student_fee_outstading_view_0`.`SubClassID` AS `SubClassID`, `student_fee_outstading_view_0`.`CouponID` AS `CouponID`, `student_fee_outstading_view_0`.`AccountNo` AS `AccountNo`, `student_fee_outstading_view_0`.`Stamp` AS `Stamp`, `student_fee_outstading_view_0`.`Description` AS `Description`, `student_fee_outstading_view_0`.`ReceiptNo` AS `ReceiptNo`, `student_fee_outstading_view_0`.`TDr` AS `TDr`, `student_fee_outstading_view_0`.`TCr` AS `TCr`, `student_fee_outstading_view_0`.`Balance` AS `Balance`, `student_fee_outstading_view_0`.`Date` AS `Date`, `student_fee_outstading_view_0`.`Time` AS `Time`, `student_fee_outstading_view_0`.`Username` AS `Username`, `student_fee_outstading_view_0`.`Status` AS `Status` FROM (`student_fee_outstading_view_0` join `consignee_main` on(`student_fee_outstading_view_0`.`StudentID` = `consignee_main`.`ConsigneeID`)) WHERE `student_fee_outstading_view_0`.`Balance` > 0 ;

-- --------------------------------------------------------

--
-- Structure for view `student_fee_outstading_view_order`
--
DROP TABLE IF EXISTS `student_fee_outstading_view_order`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_fee_outstading_view_order`  AS SELECT `student_fee_outstading_view`.`StudentID` AS `StudentID`, `student_fee_outstading_view`.`SubClassID` AS `SubClassID`, `student_fee_outstading_view`.`CouponID` AS `CouponID`, `student_fee_outstading_view`.`AccountNo` AS `AccountNo`, `handling_charge_view`.`AccountName` AS `AccountName`, CASE WHEN `handling_charge_view`.`PaymentOrder` is null THEN 0 ELSE `handling_charge_view`.`PaymentOrder` END AS `PaymentOrder`, `student_fee_outstading_view`.`Stamp` AS `Stamp`, `student_fee_outstading_view`.`Description` AS `Description`, `student_fee_outstading_view`.`ReceiptNo` AS `ReceiptNo`, `student_fee_outstading_view`.`TDr` AS `TDr`, `student_fee_outstading_view`.`TCr` AS `TCr`, round(`student_fee_outstading_view`.`TDr` - `student_fee_outstading_view`.`TCr`,2) AS `Balance`, `student_fee_outstading_view`.`Date` AS `Date`, `student_fee_outstading_view`.`Time` AS `Time`, `student_fee_outstading_view`.`Username` AS `Username`, `student_fee_outstading_view`.`Status` AS `Status` FROM (`student_fee_outstading_view` left join `handling_charge_view` on(`student_fee_outstading_view`.`AccountNo` = `handling_charge_view`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_fee_outstading_view_order_1`
--
DROP TABLE IF EXISTS `student_fee_outstading_view_order_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_fee_outstading_view_order_1`  AS SELECT `student_fee_outstading_view_order`.`StudentID` AS `StudentID`, `student_fee_outstading_view_order`.`SubClassID` AS `SubClassID`, `student_fee_outstading_view_order`.`CouponID` AS `CouponID`, `student_fee_outstading_view_order`.`AccountNo` AS `AccountNo`, `student_fee_outstading_view_order`.`PaymentOrder` AS `PmtOrder`, `student_fee_outstading_view_order`.`Stamp` AS `Stamp`, `student_fee_outstading_view_order`.`Description` AS `Description`, `student_fee_outstading_view_order`.`ReceiptNo` AS `ReceiptNo`, `student_fee_outstading_view_order`.`TDr` AS `TDr`, `student_fee_outstading_view_order`.`TCr` AS `TCr`, `student_fee_outstading_view_order`.`Balance` AS `Balance`, `student_fee_outstading_view_order`.`Date` AS `Date`, `student_fee_outstading_view_order`.`Time` AS `Time`, `student_fee_outstading_view_order`.`Username` AS `Username`, `student_fee_outstading_view_order`.`Status` AS `Status` FROM `student_fee_outstading_view_order` ;

-- --------------------------------------------------------

--
-- Structure for view `student_fee_outstading_view_rpt`
--
DROP TABLE IF EXISTS `student_fee_outstading_view_rpt`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_fee_outstading_view_rpt`  AS SELECT `student_fee`.`StudentID` AS `StudentID`, `active_students`.`FullName` AS `FullName`, `active_students`.`CurrentClassID` AS `CurrentClassID`, `active_students`.`CurrentClassName` AS `CurrentClassName`, `student_fee`.`SubClassID` AS `SubClassID`, `student_fee`.`AccountNo` AS `AccountNo`, `student_fee`.`Stamp` AS `Stamp`, `student_fee`.`CouponID` AS `ReceiptID`, `billing_board`.`CouponID` AS `CouponID`, `billing_board`.`Date` AS `BillingDate`, `student_fee`.`Description` AS `Description`, `student_fee`.`ReceiptNo` AS `ReceiptNo`, round(sum(`student_fee`.`Dr`),2) AS `TDr`, round(sum(`student_fee`.`Cr`),2) AS `TCr`, `billing_board`.`PmtStartDate` AS `PmtStartDate`, max(`student_fee`.`Date`) AS `LastDate`, `student_fee`.`Time` AS `Time`, `student_fee`.`Username` AS `Username`, `student_fee`.`Status` AS `Status` FROM (((`student_fee` join `active_students` on(`student_fee`.`StudentID` = `active_students`.`StudentID`)) join `billing_board` on(`student_fee`.`CouponID` = `billing_board`.`ReceiptNo`)) join `rpt_multi_values`) WHERE `student_fee`.`Stamp` = 'BL' AND `student_fee`.`Date` <= `rpt_multi_values`.`FDate` GROUP BY `student_fee`.`StudentID`, `student_fee`.`AccountNo`, `student_fee`.`CouponID` ;

-- --------------------------------------------------------

--
-- Structure for view `student_fee_outstading_view_rpt_0`
--
DROP TABLE IF EXISTS `student_fee_outstading_view_rpt_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_fee_outstading_view_rpt_0`  AS SELECT `student_fee_outstading_view_rpt`.`StudentID` AS `StudentID`, `student_fee_outstading_view_rpt`.`FullName` AS `FullName`, `sub_class_main_view`.`CourseID` AS `CourseID`, `sub_class_main_view`.`ClassName` AS `CourseName`, `student_fee_outstading_view_rpt`.`CurrentClassID` AS `CurrentClassID`, `student_fee_outstading_view_rpt`.`CurrentClassName` AS `CurrentClassName`, `student_fee_outstading_view_rpt`.`Stamp` AS `Stamp`, round(sum(`student_fee_outstading_view_rpt`.`TDr`),2) AS `TDr`, round(sum(`student_fee_outstading_view_rpt`.`TCr`),2) AS `TCr`, round(sum(`student_fee_outstading_view_rpt`.`TDr`) - sum(`student_fee_outstading_view_rpt`.`TCr`),2) AS `Balance`, max(`student_fee_outstading_view_rpt`.`LastDate`) AS `LastDate`, `student_fee_outstading_view_rpt`.`CouponID` AS `CouponID` FROM (`student_fee_outstading_view_rpt` join `sub_class_main_view` on(`student_fee_outstading_view_rpt`.`CurrentClassID` = `sub_class_main_view`.`SubClassID`)) GROUP BY `student_fee_outstading_view_rpt`.`StudentID` ;

-- --------------------------------------------------------

--
-- Structure for view `student_fee_outstading_view_rpt_1`
--
DROP TABLE IF EXISTS `student_fee_outstading_view_rpt_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_fee_outstading_view_rpt_1`  AS SELECT `student_fee_outstading_view_rpt_0`.`StudentID` AS `StudentID`, `student_fee_outstading_view_rpt_0`.`FullName` AS `FullName`, `student_fee_outstading_view_rpt_0`.`CourseID` AS `CourseID`, `student_fee_outstading_view_rpt_0`.`CourseName` AS `CourseName`, `student_fee_outstading_view_rpt_0`.`CurrentClassID` AS `CurrentClassID`, `student_fee_outstading_view_rpt_0`.`CurrentClassName` AS `CurrentClassName`, `student_fee_outstading_view_rpt_0`.`Stamp` AS `Stamp`, `student_fee_outstading_view_rpt_0`.`TDr` AS `TDr`, `student_fee_outstading_view_rpt_0`.`TCr` AS `TCr`, `student_fee_outstading_view_rpt_0`.`Balance` AS `Balance`, `student_fee_outstading_view_rpt_0`.`LastDate` AS `LastDate`, `student_fee_outstading_view_rpt_0`.`CouponID` AS `CouponID` FROM `student_fee_outstading_view_rpt_0` WHERE 1 ;

-- --------------------------------------------------------

--
-- Structure for view `student_fee_view`
--
DROP TABLE IF EXISTS `student_fee_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_fee_view`  AS SELECT `student_fee`.`StudentID` AS `StudentID`, `student_fee`.`SubClassID` AS `SubClassID`, `student_fee`.`CouponID` AS `CouponID`, `student_fee`.`AccountNo` AS `AccountNo`, `student_fee`.`Stamp` AS `Stamp`, `student_fee`.`Description` AS `Description`, `student_fee`.`ReceiptNo` AS `ReceiptNo`, `student_fee`.`Dr` AS `Dr`, `student_fee`.`Cr` AS `Cr`, `student_fee`.`Date` AS `Date`, `student_fee`.`Time` AS `Time`, `student_fee`.`Username` AS `Username`, `student_fee`.`Status` AS `Status` FROM `student_fee` WHERE `student_fee`.`Stamp` = 'BL' OR `student_fee`.`Stamp` = 'BL_NONBL' ;

-- --------------------------------------------------------

--
-- Structure for view `student_fee_view_1`
--
DROP TABLE IF EXISTS `student_fee_view_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_fee_view_1`  AS SELECT `student_fee_view`.`StudentID` AS `StudentID`, `student_fee_view`.`SubClassID` AS `SubClassID`, `student_fee_view`.`AccountNo` AS `AccountNo`, `student_fee_view`.`CouponID` AS `CouponID`, `student_fee_view`.`Stamp` AS `Stamp`, `student_fee_view`.`Description` AS `Description`, `student_fee_view`.`ReceiptNo` AS `ReceiptNo`, round(sum(`student_fee_view`.`Dr`),2) AS `TDr`, round(sum(`student_fee_view`.`Cr`),2) AS `TCr`, `student_fee_view`.`Date` AS `Date`, `student_fee_view`.`Time` AS `Time`, `student_fee_view`.`Username` AS `Username`, `student_fee_view`.`Status` AS `Status` FROM `student_fee_view` GROUP BY `student_fee_view`.`ReceiptNo`, `student_fee_view`.`StudentID` ;

-- --------------------------------------------------------

--
-- Structure for view `student_fee_view_2`
--
DROP TABLE IF EXISTS `student_fee_view_2`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_fee_view_2`  AS SELECT `student_fee_view_1`.`StudentID` AS `StudentID`, `consignee_main`.`FullName` AS `FullName`, `consignee_main`.`TelNo` AS `TelNo`, `student_fee_view_1`.`SubClassID` AS `SubClassID`, `student_fee_view_1`.`AccountNo` AS `AccountNo`, `student_fee_view_1`.`CouponID` AS `CouponID`, `student_fee_view_1`.`Stamp` AS `Stamp`, `student_fee_view_1`.`Description` AS `Description`, `student_fee_view_1`.`ReceiptNo` AS `ReceiptNo`, `student_fee_view_1`.`TDr` AS `TDr`, `student_fee_view_1`.`TCr` AS `TCr`, `student_fee_view_1`.`Date` AS `Date`, `student_fee_view_1`.`Time` AS `Time`, `student_fee_view_1`.`Username` AS `Username`, `student_fee_view_1`.`Status` AS `Status` FROM (`student_fee_view_1` join `consignee_main` on(`student_fee_view_1`.`StudentID` = `consignee_main`.`ConsigneeID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_gender_chart`
--
DROP TABLE IF EXISTS `student_gender_chart`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_gender_chart`  AS SELECT `student_gen_pop_all`.`SubCurrentClassID` AS `SubCurrentClassID`, `student_gen_pop_all`.`SubCurrentClassName` AS `SubCurrentClassName`, `student_gen_pop_all`.`Male` AS `Male`, `student_gen_pop_all`.`Female` AS `Female`, `student_gen_pop_all`.`TPop` AS `TPop` FROM `student_gen_pop_all` ;

-- --------------------------------------------------------

--
-- Structure for view `student_gen_pop`
--
DROP TABLE IF EXISTS `student_gen_pop`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_gen_pop`  AS SELECT `student_current_class`.`StudentID` AS `StudentID`, `student_current_class`.`FullName` AS `FullName`, `student_main`.`Gender` AS `Gender`, `student_main`.`DOB` AS `DOB`, `student_main`.`BoarderStats` AS `BoarderStats`, `sub_class_main_view`.`CourseID` AS `CourseID`, `sub_class_main_view`.`ClassName` AS `Course`, `student_current_class`.`SubCurrentClassID` AS `SubCurrentClassID`, `student_current_class`.`SubCurrentClassName` AS `SubCurrentClassName`, `student_current_class`.`Date` AS `Date`, `student_current_class`.`Status` AS `Status` FROM ((`student_current_class` join `sub_class_main_view` on(`student_current_class`.`SubCurrentClassID` = `sub_class_main_view`.`SubClassID`)) join `student_main` on(`student_current_class`.`StudentID` = `student_main`.`StudentID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_gen_pop_0`
--
DROP TABLE IF EXISTS `student_gen_pop_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_gen_pop_0`  AS SELECT `student_gen_pop`.`SubCurrentClassID` AS `SubCurrentClassID`, `student_gen_pop`.`SubCurrentClassName` AS `SubCurrentClassName`, count(`student_gen_pop`.`StudentID`) AS `TPop` FROM `student_gen_pop` GROUP BY `student_gen_pop`.`SubCurrentClassID` ;

-- --------------------------------------------------------

--
-- Structure for view `student_gen_pop_all`
--
DROP TABLE IF EXISTS `student_gen_pop_all`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_gen_pop_all`  AS SELECT `student_gen_pop_0`.`SubCurrentClassID` AS `SubCurrentClassID`, `student_gen_pop_0`.`SubCurrentClassName` AS `SubCurrentClassName`, `student_gen_pop_male`.`Pop` AS `Male`, `student_gen_pop_female`.`Pop` AS `Female`, `student_gen_pop_0`.`TPop` AS `TPop` FROM ((`student_gen_pop_0` join `student_gen_pop_male` on(`student_gen_pop_0`.`SubCurrentClassID` = `student_gen_pop_male`.`SubCurrentClassID`)) join `student_gen_pop_female` on(`student_gen_pop_0`.`SubCurrentClassID` = `student_gen_pop_female`.`SubCurrentClassID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_gen_pop_female`
--
DROP TABLE IF EXISTS `student_gen_pop_female`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_gen_pop_female`  AS SELECT `student_gen_pop`.`SubCurrentClassID` AS `SubCurrentClassID`, `student_gen_pop`.`SubCurrentClassName` AS `SubCurrentClassName`, `student_gen_pop`.`Gender` AS `Gender`, count(`student_gen_pop`.`Gender`) AS `Pop` FROM `student_gen_pop` WHERE `student_gen_pop`.`Gender` = 'FEMALE' GROUP BY `student_gen_pop`.`Gender`, `student_gen_pop`.`SubCurrentClassID` ;

-- --------------------------------------------------------

--
-- Structure for view `student_gen_pop_male`
--
DROP TABLE IF EXISTS `student_gen_pop_male`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_gen_pop_male`  AS SELECT `student_gen_pop`.`SubCurrentClassID` AS `SubCurrentClassID`, `student_gen_pop`.`SubCurrentClassName` AS `SubCurrentClassName`, `student_gen_pop`.`Gender` AS `Gender`, count(`student_gen_pop`.`Gender`) AS `Pop` FROM `student_gen_pop` WHERE `student_gen_pop`.`Gender` = 'MALE' GROUP BY `student_gen_pop`.`Gender`, `student_gen_pop`.`SubCurrentClassID` ;

-- --------------------------------------------------------

--
-- Structure for view `student_main_view`
--
DROP TABLE IF EXISTS `student_main_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_main_view`  AS SELECT `student_main`.`Year` AS `Year`, `student_main`.`Code` AS `Code`, `student_main`.`StudentID` AS `StudentID`, `student_main`.`FirstName` AS `FirstName`, `student_main`.`LastName` AS `LastName`, concat(`student_main`.`FirstName`,' ',`student_main`.`LastName`) AS `FullName`, `student_main`.`Gender` AS `Gender`, `student_main`.`DOB` AS `DOB`, `student_main`.`CourseID` AS `CourseID`, `class_main`.`ClassName` AS `CourseName`, `student_main`.`ClassAdmiitted` AS `ClassAdmiitted`, `sub_class_main`.`SubClassName` AS `CurrentClass`, `student_main`.`AdmissionDate` AS `AdmissionDate`, `student_main`.`BoarderStats` AS `BoarderStats`, `student_main`.`LastSchool` AS `LastSchool`, `student_main`.`STelNo` AS `STelNo`, `student_main`.`AgregateResult` AS `AgregateResult`, `student_main`.`FatherName` AS `FatherName`, `student_main`.`MotherName` AS `MotherName`, `student_main`.`TelNo` AS `TelNo`, `student_main`.`Occupation` AS `Occupation`, `student_main`.`Address` AS `Address`, `student_main`.`HouseID` AS `HouseID`, `house`.`House` AS `House`, `student_main`.`Email` AS `Email`, `student_main`.`Date` AS `Date`, `student_main`.`Time` AS `Time`, `student_main`.`Username` AS `Username`, `student_main`.`Status` AS `Status`, `student_main`.`BranchID` AS `BranchID` FROM (((`student_main` left join `sub_class_main` on(`student_main`.`ClassAdmiitted` = `sub_class_main`.`SubClassID`)) join `class_main` on(`student_main`.`CourseID` = `class_main`.`ClassID`)) left join `house` on(`student_main`.`HouseID` = `house`.`HouseID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_promo_view`
--
DROP TABLE IF EXISTS `student_promo_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_promo_view`  AS SELECT `student_promo`.`StudentID` AS `StudentID`, `student_promo`.`PreviousClass` AS `PreviousClass`, `student_promo`.`PromoClass` AS `PromoClass`, `sub_class_main`.`SubClassName` AS `SubClassName`, `student_promo`.`Date` AS `Date`, `student_promo`.`Time` AS `Time`, `student_promo`.`Username` AS `Username`, `student_promo`.`Status` AS `Status` FROM (`student_promo` join `sub_class_main` on(`student_promo`.`PromoClass` = `sub_class_main`.`SubClassID`)) WHERE `student_promo`.`Status` = 1 AND `sub_class_main`.`SubClassID` <> '999' ;

-- --------------------------------------------------------

--
-- Structure for view `student_promo_view_empty`
--
DROP TABLE IF EXISTS `student_promo_view_empty`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_promo_view_empty`  AS SELECT DISTINCT `student_promo`.`PromoClass` AS `PromoClass`, `student_promo`.`Status` AS `Status` FROM `student_promo` WHERE `student_promo`.`Status` = 1 ;

-- --------------------------------------------------------

--
-- Structure for view `student_promo_view_empty_0`
--
DROP TABLE IF EXISTS `student_promo_view_empty_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_promo_view_empty_0`  AS SELECT `sub_class_main`.`CourseID` AS `CourseID`, `sub_class_main`.`SubClassID` AS `SubClassID`, `sub_class_main`.`SubClassName` AS `SubClassName`, `sub_class_main`.`Enroll` AS `Enroll`, `student_promo_view_empty`.`Status` AS `Status` FROM (`sub_class_main` left join `student_promo_view_empty` on(`sub_class_main`.`SubClassID` = `student_promo_view_empty`.`PromoClass`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_register_subject_view`
--
DROP TABLE IF EXISTS `student_register_subject_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_register_subject_view`  AS SELECT `student_register_subject`.`StudentID` AS `StudentID`, `student_main_view`.`FullName` AS `FullName`, `student_register_subject`.`SubClassID` AS `SubClassID`, `sub_class_main_view`.`ClassName` AS `CourseName`, `sub_class_main_view`.`SubClassName` AS `SubClassName`, `student_register_subject`.`SubjectID` AS `SubjectID`, `subject_main`.`SubjectName` AS `SubjectName`, `student_register_subject`.`Attendance` AS `AttendanceID`, CASE WHEN `student_register_subject`.`Attendance` = 1 THEN 'PRESENT' ELSE 'ABSENT' END AS `Attendance`, `student_register_subject`.`Username` AS `Username`, `kaina`.`FullName` AS `Uname`, `student_register_subject`.`Date` AS `Date`, `student_register_subject`.`Time` AS `Time`, `student_register_subject`.`Status` AS `Status` FROM ((((`student_register_subject` join `student_main_view` on(`student_register_subject`.`StudentID` = `student_main_view`.`StudentID`)) join `sub_class_main_view` on(`student_register_subject`.`SubClassID` = `sub_class_main_view`.`SubClassID`)) join `subject_main` on(`student_register_subject`.`SubjectID` = `subject_main`.`SubjectID`)) join `kaina` on(`student_register_subject`.`Username` = `kaina`.`ID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_register_view`
--
DROP TABLE IF EXISTS `student_register_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_register_view`  AS SELECT `student_register`.`StudentID` AS `StudentID`, `student_current_class`.`FullName` AS `FullName`, `student_register`.`SubClassID` AS `SubClassID`, `sub_class_main_view`.`ClassName` AS `Course`, `sub_class_main_view`.`SubClassName` AS `SubClassName`, `student_register`.`Attendance` AS `AttendanceID`, CASE WHEN `student_register`.`Attendance` = 1 THEN 'PRESENT' ELSE 'ABSENT' END AS `Attendance`, `student_register`.`Username` AS `Username`, `kaina`.`FullName` AS `Uname`, `student_register`.`Date` AS `Date`, `student_register`.`Time` AS `Time`, `student_register`.`Status` AS `Status` FROM (((`student_register` join `student_current_class` on(`student_register`.`StudentID` = `student_current_class`.`StudentID`)) join `sub_class_main_view` on(`student_register`.`SubClassID` = `sub_class_main_view`.`SubClassID`)) join `kaina` on(`student_register`.`Username` = `kaina`.`ID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt`
--
DROP TABLE IF EXISTS `student_terminal_rpt`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt`  AS SELECT `student_test_score_view`.`CouponNo` AS `CouponNo`, `student_test_score_view`.`Title` AS `Title`, `student_test_score_view`.`StudentID` AS `StudentID`, `student_test_score_view`.`FullName` AS `FullName`, `student_test_score_view`.`CourseID` AS `CourseID`, `student_test_score_view`.`CourseName` AS `CourseName`, `student_test_score_view`.`SubClassID` AS `SubClassID`, `student_test_score_view`.`SubClassName` AS `SubClassName`, `student_test_score_view`.`TestType` AS `TestType`, `student_test_score_view`.`TestID` AS `TestID`, `student_test_score_view`.`SubjectID` AS `SubjectID`, `student_test_score_view`.`SubjectName` AS `SubjectName`, `student_test_score_view`.`Score` AS `Score`, `student_test_score_view`.`Username` AS `Username`, `student_test_score_view`.`FName` AS `FName`, `student_test_score_view`.`StaffName` AS `StaffName`, `student_test_score_view`.`Date` AS `Date`, `student_test_score_view`.`Time` AS `Time`, `student_test_score_view`.`Status` AS `Status`, `rpt_multi_values`.`Username` AS `ViewerID` FROM (`student_test_score_view` join `rpt_multi_values`) WHERE `student_test_score_view`.`CouponNo` = `rpt_multi_values`.`SubjectID` AND `student_test_score_view`.`SubClassID` = `rpt_multi_values`.`Sub_ClassID` ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_class_test`
--
DROP TABLE IF EXISTS `student_terminal_rpt_class_test`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_class_test`  AS SELECT `student_test_score_view`.`StudentID` AS `StudentID`, `student_test_score_view`.`TestCategory` AS `TestCategory`, `student_test_score_view`.`TestID` AS `TestID`, `student_test_score_view`.`TestType` AS `TestType`, `student_test_score_view`.`SubjectID` AS `SubjectID`, `student_test_score_view`.`SubjectName` AS `SubjectName`, `student_test_score_view`.`Score` AS `Score`, `student_test_score_view`.`Username` AS `Username` FROM `student_test_score_view` WHERE `student_test_score_view`.`TestCategory` = 'CLASS WORK' OR `student_test_score_view`.`TestCategory` = 'CLASSS WORK' ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_class_test_0`
--
DROP TABLE IF EXISTS `student_terminal_rpt_class_test_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_class_test_0`  AS SELECT `student_terminal_rpt_class_test`.`StudentID` AS `StudentID`, `student_terminal_rpt_class_test`.`TestCategory` AS `TestCategory`, round(`test_type`.`Percentage` / 100,2) AS `Percentage`, `student_terminal_rpt_class_test`.`TestID` AS `TestID`, `student_terminal_rpt_class_test`.`TestType` AS `TestType`, `student_terminal_rpt_class_test`.`SubjectID` AS `SubjectID`, `student_terminal_rpt_class_test`.`SubjectName` AS `SubjectName`, round(sum(`student_terminal_rpt_class_test`.`Score`),2) AS `Score`, `student_terminal_rpt_class_test`.`Username` AS `Username` FROM (`student_terminal_rpt_class_test` join `test_type` on(`student_terminal_rpt_class_test`.`TestCategory` = `test_type`.`TypeName`)) GROUP BY `student_terminal_rpt_class_test`.`StudentID`, `student_terminal_rpt_class_test`.`SubjectID` ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_class_test_1`
--
DROP TABLE IF EXISTS `student_terminal_rpt_class_test_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_class_test_1`  AS SELECT `student_terminal_rpt_class_test_0`.`StudentID` AS `StudentID`, `student_terminal_rpt_class_test_0`.`TestCategory` AS `TestCategory`, `student_terminal_rpt_class_test_0`.`Percentage` AS `Percentage`, `student_terminal_rpt_class_test_0`.`TestID` AS `TestID`, `student_terminal_rpt_class_test_0`.`TestType` AS `TestType`, `student_terminal_rpt_class_test_0`.`SubjectID` AS `SubjectID`, `student_terminal_rpt_class_test_0`.`SubjectName` AS `SubjectName`, `student_terminal_rpt_class_test_0`.`Score` AS `Score`, round(`student_terminal_rpt_class_test_0`.`Score` * `student_terminal_rpt_class_test_0`.`Percentage`,1) AS `CWScore`, `student_terminal_rpt_class_test_0`.`Username` AS `Username` FROM `student_terminal_rpt_class_test_0` WHERE 1 ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_exams`
--
DROP TABLE IF EXISTS `student_terminal_rpt_exams`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_exams`  AS SELECT `student_test_score_view`.`StudentID` AS `StudentID`, `student_test_score_view`.`TestCategory` AS `TestCategory`, `student_test_score_view`.`TestID` AS `TestID`, `student_test_score_view`.`TestType` AS `TestType`, `student_test_score_view`.`SubjectID` AS `SubjectID`, `student_test_score_view`.`SubjectName` AS `SubjectName`, `student_test_score_view`.`Score` AS `Score`, `student_test_score_view`.`Username` AS `Username` FROM `student_test_score_view` WHERE `student_test_score_view`.`TestCategory` = 'TERMINAL EXAM' ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_exams_0`
--
DROP TABLE IF EXISTS `student_terminal_rpt_exams_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_exams_0`  AS SELECT `student_terminal_rpt_exams`.`StudentID` AS `StudentID`, `student_terminal_rpt_exams`.`TestCategory` AS `TestCategory`, round(`test_type`.`Percentage` / 100,2) AS `Percentage`, `student_terminal_rpt_exams`.`TestID` AS `TestID`, `student_terminal_rpt_exams`.`TestType` AS `TestType`, `student_terminal_rpt_exams`.`SubjectID` AS `SubjectID`, `student_terminal_rpt_exams`.`SubjectName` AS `SubjectName`, round(sum(`student_terminal_rpt_exams`.`Score`),2) AS `Score`, `student_terminal_rpt_exams`.`Username` AS `Username` FROM (`student_terminal_rpt_exams` join `test_type` on(`student_terminal_rpt_exams`.`TestCategory` = `test_type`.`TypeName`)) GROUP BY `student_terminal_rpt_exams`.`StudentID`, `student_terminal_rpt_exams`.`SubjectID` ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_exams_1`
--
DROP TABLE IF EXISTS `student_terminal_rpt_exams_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_exams_1`  AS SELECT `student_terminal_rpt_exams_0`.`StudentID` AS `StudentID`, `student_terminal_rpt_exams_0`.`TestCategory` AS `TestCategory`, `student_terminal_rpt_exams_0`.`Percentage` AS `Percentage`, `student_terminal_rpt_exams_0`.`TestID` AS `TestID`, `student_terminal_rpt_exams_0`.`TestType` AS `TestType`, `student_terminal_rpt_exams_0`.`SubjectID` AS `SubjectID`, `student_terminal_rpt_exams_0`.`SubjectName` AS `SubjectName`, `student_terminal_rpt_exams_0`.`Score` AS `Score`, round(`student_terminal_rpt_exams_0`.`Score` * `student_terminal_rpt_exams_0`.`Percentage`,1) AS `EXScore`, `student_terminal_rpt_exams_0`.`Username` AS `Username` FROM `student_terminal_rpt_exams_0` ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_fnal`
--
DROP TABLE IF EXISTS `student_terminal_rpt_fnal`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_fnal`  AS SELECT `student_terminal_rpt_names_0`.`CouponNo` AS `CouponNo`, `student_terminal_rpt_names_0`.`Title` AS `Title`, `student_terminal_rpt_names_0`.`StudentID` AS `StudentID`, `student_terminal_rpt_names_0`.`FullName` AS `FullName`, `student_terminal_rpt_names_0`.`CourseID` AS `CourseID`, `student_terminal_rpt_names_0`.`CourseName` AS `CourseName`, `student_terminal_rpt_names_0`.`SubClassID` AS `SubClassID`, `student_terminal_rpt_names_0`.`SubClassName` AS `SubClassName`, `student_terminal_rpt_names_0`.`SubjectID` AS `SubjectID`, `student_terminal_rpt_names_0`.`SubjectName` AS `SubjectName`, CASE WHEN `student_terminal_rpt_class_test_1`.`Percentage` is null THEN '0' ELSE `student_terminal_rpt_class_test_1`.`Percentage` END AS `CWPcntg`, CASE WHEN `student_terminal_rpt_class_test_1`.`Score` is null THEN 0 ELSE `student_terminal_rpt_class_test_1`.`Score` END AS `CWScore_0`, CASE WHEN `student_terminal_rpt_class_test_1`.`CWScore` is null THEN 0 ELSE `student_terminal_rpt_class_test_1`.`CWScore` END AS `CWScore`, `student_terminal_rpt_names_0`.`Username` AS `Username`, `student_terminal_rpt_names_0`.`ViewerID` AS `ViewerID` FROM (`student_terminal_rpt_names_0` left join `student_terminal_rpt_class_test_1` on(`student_terminal_rpt_names_0`.`StudentID` = `student_terminal_rpt_class_test_1`.`StudentID` and `student_terminal_rpt_names_0`.`SubjectID` = `student_terminal_rpt_class_test_1`.`SubjectID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_fnal_0`
--
DROP TABLE IF EXISTS `student_terminal_rpt_fnal_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_fnal_0`  AS SELECT `student_terminal_rpt_fnal`.`CouponNo` AS `CouponNo`, `student_terminal_rpt_fnal`.`Title` AS `Title`, `student_terminal_rpt_fnal`.`StudentID` AS `StudentID`, `student_terminal_rpt_fnal`.`FullName` AS `FullName`, `student_terminal_rpt_fnal`.`CourseID` AS `CourseID`, `student_terminal_rpt_fnal`.`CourseName` AS `CourseName`, `student_terminal_rpt_fnal`.`SubClassID` AS `SubClassID`, `student_terminal_rpt_fnal`.`SubClassName` AS `SubClassName`, `student_terminal_rpt_fnal`.`SubjectID` AS `SubjectID`, `student_terminal_rpt_fnal`.`SubjectName` AS `SubjectName`, `student_terminal_rpt_fnal`.`CWPcntg` AS `CWPcntg`, `student_terminal_rpt_fnal`.`CWScore_0` AS `CWScore_0`, `student_terminal_rpt_fnal`.`CWScore` AS `CWScore`, CASE WHEN `student_terminal_rpt_exams_1`.`Percentage` is null THEN 0 ELSE `student_terminal_rpt_exams_1`.`Percentage` END AS `ExPcntg`, CASE WHEN `student_terminal_rpt_exams_1`.`Score` is null THEN 0 ELSE `student_terminal_rpt_exams_1`.`Score` END AS `ExScore`, CASE WHEN `student_terminal_rpt_exams_1`.`EXScore` is null THEN 0 ELSE `student_terminal_rpt_exams_1`.`EXScore` END AS `ExScore_0`, `student_terminal_rpt_fnal`.`Username` AS `Username`, `student_terminal_rpt_fnal`.`ViewerID` AS `ViewerID` FROM (`student_terminal_rpt_fnal` left join `student_terminal_rpt_exams_1` on(`student_terminal_rpt_fnal`.`StudentID` = `student_terminal_rpt_exams_1`.`StudentID` and `student_terminal_rpt_fnal`.`SubjectID` = `student_terminal_rpt_exams_1`.`SubjectID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_fnal_0_ind`
--
DROP TABLE IF EXISTS `student_terminal_rpt_fnal_0_ind`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_fnal_0_ind`  AS SELECT `student_terminal_rpt_fnal_ind`.`CouponNo` AS `CouponNo`, `student_terminal_rpt_fnal_ind`.`Title` AS `Title`, `student_terminal_rpt_fnal_ind`.`StudentID` AS `StudentID`, `student_terminal_rpt_fnal_ind`.`FullName` AS `FullName`, `student_terminal_rpt_fnal_ind`.`CourseID` AS `CourseID`, `student_terminal_rpt_fnal_ind`.`CourseName` AS `CourseName`, `student_terminal_rpt_fnal_ind`.`SubClassID` AS `SubClassID`, `student_terminal_rpt_fnal_ind`.`SubClassName` AS `SubClassName`, `student_terminal_rpt_fnal_ind`.`SubjectID` AS `SubjectID`, `student_terminal_rpt_fnal_ind`.`SubjectName` AS `SubjectName`, `student_terminal_rpt_fnal_ind`.`CWPcntg` AS `CWPcntg`, `student_terminal_rpt_fnal_ind`.`CWScore_0` AS `CWScore_0`, `student_terminal_rpt_fnal_ind`.`CWScore` AS `CWScore`, CASE WHEN `student_terminal_rpt_exams_1`.`Percentage` is null THEN 0 ELSE `student_terminal_rpt_exams_1`.`Percentage` END AS `ExPcntg`, CASE WHEN `student_terminal_rpt_exams_1`.`Score` is null THEN 0 ELSE `student_terminal_rpt_exams_1`.`Score` END AS `ExScore`, CASE WHEN `student_terminal_rpt_exams_1`.`EXScore` is null THEN 0 ELSE `student_terminal_rpt_exams_1`.`EXScore` END AS `ExScore_0`, `student_terminal_rpt_fnal_ind`.`Username` AS `Username`, `student_terminal_rpt_fnal_ind`.`ViewerID` AS `ViewerID` FROM (`student_terminal_rpt_fnal_ind` left join `student_terminal_rpt_exams_1` on(`student_terminal_rpt_fnal_ind`.`StudentID` = `student_terminal_rpt_exams_1`.`StudentID` and `student_terminal_rpt_fnal_ind`.`SubjectID` = `student_terminal_rpt_exams_1`.`SubjectID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_fnal_1`
--
DROP TABLE IF EXISTS `student_terminal_rpt_fnal_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_fnal_1`  AS SELECT `student_terminal_rpt_fnal_0`.`CouponNo` AS `CouponNo`, `student_terminal_rpt_fnal_0`.`Title` AS `Title`, `student_terminal_rpt_fnal_0`.`StudentID` AS `StudentID`, `student_terminal_rpt_fnal_0`.`FullName` AS `FullName`, `student_terminal_rpt_fnal_0`.`CourseID` AS `CourseID`, `student_terminal_rpt_fnal_0`.`CourseName` AS `CourseName`, `student_terminal_rpt_fnal_0`.`SubClassID` AS `SubClassID`, `student_terminal_rpt_fnal_0`.`SubClassName` AS `SubClassName`, `student_terminal_rpt_fnal_0`.`SubjectID` AS `SubjectID`, `student_terminal_rpt_fnal_0`.`SubjectName` AS `SubjectName`, round(`student_terminal_rpt_fnal_0`.`CWScore_0`,2) AS `CWScore_0`, round(`student_terminal_rpt_fnal_0`.`CWScore`,0) AS `CWScore`, round(`student_terminal_rpt_fnal_0`.`ExScore`,0) AS `ExScore`, round(`student_terminal_rpt_fnal_0`.`ExScore_0`,0) AS `ExScore_0`, round(`student_terminal_rpt_fnal_0`.`ExScore_0` + `student_terminal_rpt_fnal_0`.`CWScore`,0) AS `Total`, `student_terminal_rpt_fnal_0`.`Username` AS `Username`, `student_terminal_rpt_fnal_0`.`ViewerID` AS `ViewerID` FROM `student_terminal_rpt_fnal_0` ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_fnal_1_ind`
--
DROP TABLE IF EXISTS `student_terminal_rpt_fnal_1_ind`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_fnal_1_ind`  AS SELECT `student_terminal_rpt_fnal_0_ind`.`CouponNo` AS `CouponNo`, `student_terminal_rpt_fnal_0_ind`.`Title` AS `Title`, `student_terminal_rpt_fnal_0_ind`.`StudentID` AS `StudentID`, `student_terminal_rpt_fnal_0_ind`.`FullName` AS `FullName`, `student_terminal_rpt_fnal_0_ind`.`CourseID` AS `CourseID`, `student_terminal_rpt_fnal_0_ind`.`CourseName` AS `CourseName`, `student_terminal_rpt_fnal_0_ind`.`SubClassID` AS `SubClassID`, `student_terminal_rpt_fnal_0_ind`.`SubClassName` AS `SubClassName`, `student_terminal_rpt_fnal_0_ind`.`SubjectID` AS `SubjectID`, `student_terminal_rpt_fnal_0_ind`.`SubjectName` AS `SubjectName`, round(`student_terminal_rpt_fnal_0_ind`.`CWScore_0`,2) AS `CWScore_0`, round(`student_terminal_rpt_fnal_0_ind`.`CWScore`,0) AS `CWScore`, round(`student_terminal_rpt_fnal_0_ind`.`ExScore`,0) AS `ExScore`, round(`student_terminal_rpt_fnal_0_ind`.`ExScore_0`,0) AS `ExScore_0`, round(`student_terminal_rpt_fnal_0_ind`.`ExScore_0` + `student_terminal_rpt_fnal_0_ind`.`CWScore`,0) AS `Total`, `student_terminal_rpt_fnal_0_ind`.`Username` AS `Username`, `student_terminal_rpt_fnal_0_ind`.`ViewerID` AS `ViewerID` FROM `student_terminal_rpt_fnal_0_ind` ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_fnal_2`
--
DROP TABLE IF EXISTS `student_terminal_rpt_fnal_2`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_fnal_2`  AS SELECT `student_terminal_rpt_fnal_1`.`CouponNo` AS `CouponNo`, `student_terminal_rpt_fnal_1`.`Title` AS `Title`, `student_terminal_rpt_fnal_1`.`StudentID` AS `StudentID`, `student_terminal_rpt_fnal_1`.`FullName` AS `FullName`, `student_terminal_rpt_fnal_1`.`CourseID` AS `CourseID`, `student_terminal_rpt_fnal_1`.`CourseName` AS `CourseName`, `student_terminal_rpt_fnal_1`.`SubClassID` AS `SubClassID`, `student_terminal_rpt_fnal_1`.`SubClassName` AS `SubClassName`, `subject_main_view`.`CategoryID` AS `SubjectCatID`, `subject_main_view`.`CategoryName` AS `SubjectCatNm`, `student_terminal_rpt_fnal_1`.`SubjectID` AS `SubjectID`, `student_terminal_rpt_fnal_1`.`SubjectName` AS `SubjectName`, `student_terminal_rpt_fnal_1`.`CWScore_0` AS `CWScore_0`, `student_terminal_rpt_fnal_1`.`CWScore` AS `CWScore`, `student_terminal_rpt_fnal_1`.`ExScore` AS `ExScore`, `student_terminal_rpt_fnal_1`.`ExScore_0` AS `ExScore_0`, `student_terminal_rpt_fnal_1`.`Total` AS `Total`, `student_terminal_rpt_fnal_1`.`Username` AS `Username`, `staff_main`.`FullName` AS `StaffName`, `student_terminal_rpt_fnal_1`.`ViewerID` AS `ViewerID` FROM ((`student_terminal_rpt_fnal_1` join `staff_main` on(`student_terminal_rpt_fnal_1`.`Username` = `staff_main`.`StaffID`)) join `subject_main_view` on(`student_terminal_rpt_fnal_1`.`SubjectID` = `subject_main_view`.`SubjectID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_fnal_2_ind`
--
DROP TABLE IF EXISTS `student_terminal_rpt_fnal_2_ind`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_fnal_2_ind`  AS SELECT `student_terminal_rpt_fnal_1_ind`.`CouponNo` AS `CouponNo`, `student_terminal_rpt_fnal_1_ind`.`Title` AS `Title`, `student_terminal_rpt_fnal_1_ind`.`StudentID` AS `StudentID`, `student_terminal_rpt_fnal_1_ind`.`FullName` AS `FullName`, `student_terminal_rpt_fnal_1_ind`.`CourseID` AS `CourseID`, `student_terminal_rpt_fnal_1_ind`.`CourseName` AS `CourseName`, `student_terminal_rpt_fnal_1_ind`.`SubClassID` AS `SubClassID`, `student_terminal_rpt_fnal_1_ind`.`SubClassName` AS `SubClassName`, `subject_main_view`.`CategoryID` AS `SubjectCatID`, `subject_main_view`.`CategoryName` AS `SubjectCatNm`, `student_terminal_rpt_fnal_1_ind`.`SubjectID` AS `SubjectID`, `student_terminal_rpt_fnal_1_ind`.`SubjectName` AS `SubjectName`, `student_terminal_rpt_fnal_1_ind`.`CWScore_0` AS `CWScore_0`, `student_terminal_rpt_fnal_1_ind`.`CWScore` AS `CWScore`, `student_terminal_rpt_fnal_1_ind`.`ExScore` AS `ExScore`, `student_terminal_rpt_fnal_1_ind`.`ExScore_0` AS `ExScore_0`, `student_terminal_rpt_fnal_1_ind`.`Total` AS `Total`, `student_terminal_rpt_fnal_1_ind`.`Username` AS `Username`, `staff_main`.`FullName` AS `StaffName`, `student_terminal_rpt_fnal_1_ind`.`ViewerID` AS `ViewerID` FROM ((`student_terminal_rpt_fnal_1_ind` join `staff_main` on(`student_terminal_rpt_fnal_1_ind`.`Username` = `staff_main`.`StaffID`)) join `subject_main_view` on(`student_terminal_rpt_fnal_1_ind`.`SubjectID` = `subject_main_view`.`SubjectID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_fnal_3`
--
DROP TABLE IF EXISTS `student_terminal_rpt_fnal_3`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_fnal_3`  AS SELECT `student_terminal_rpt_fnal_2`.`CouponNo` AS `CouponNo`, `student_terminal_rpt_fnal_2`.`Title` AS `Title`, `student_terminal_rpt_fnal_2`.`StudentID` AS `StudentID`, `student_terminal_rpt_fnal_2`.`FullName` AS `FullName`, `student_main`.`Gender` AS `Gender`, `student_terminal_rpt_fnal_2`.`CourseID` AS `CourseID`, `student_terminal_rpt_fnal_2`.`CourseName` AS `CourseName`, `student_terminal_rpt_fnal_2`.`SubClassID` AS `SubClassID`, `student_terminal_rpt_fnal_2`.`SubClassName` AS `SubClassName`, `student_terminal_rpt_fnal_2`.`SubjectCatID` AS `SubjectCatID`, `student_terminal_rpt_fnal_2`.`SubjectCatNm` AS `SubjectCatNm`, `student_terminal_rpt_fnal_2`.`SubjectID` AS `SubjectID`, `student_terminal_rpt_fnal_2`.`SubjectName` AS `SubjectName`, `student_terminal_rpt_fnal_2`.`CWScore_0` AS `CWScore_0`, `student_terminal_rpt_fnal_2`.`CWScore` AS `CWScore`, `student_terminal_rpt_fnal_2`.`ExScore` AS `ExScore`, `student_terminal_rpt_fnal_2`.`ExScore_0` AS `ExScore_0`, `student_terminal_rpt_fnal_2`.`Total` AS `Total`, `student_terminal_rpt_fnal_class_avg`.`Population` AS `Population`, `student_terminal_rpt_fnal_class_avg`.`ClassTotal` AS `ClassTotal`, round(`student_terminal_rpt_fnal_class_avg`.`ClassTotal` / `student_terminal_rpt_fnal_class_avg`.`Population`,0) AS `ClassAvg`, `student_terminal_rpt_fnal_student_avg`.`SubjectCount` AS `SubjectCount`, `student_terminal_rpt_fnal_student_avg`.`GrandTotal` AS `TotalMarks`, round(`student_terminal_rpt_fnal_student_avg`.`GrandTotal` / `student_terminal_rpt_fnal_student_avg`.`SubjectCount`,0) AS `StudentAvg`, `student_terminal_rpt_fnal_2`.`Username` AS `Username`, `student_terminal_rpt_fnal_2`.`StaffName` AS `StaffName`, `student_terminal_rpt_fnal_2`.`ViewerID` AS `ViewerID` FROM (((`student_terminal_rpt_fnal_2` left join `student_terminal_rpt_fnal_class_avg` on(`student_terminal_rpt_fnal_2`.`SubjectID` = `student_terminal_rpt_fnal_class_avg`.`SubjectID`)) left join `student_terminal_rpt_fnal_student_avg` on(`student_terminal_rpt_fnal_2`.`StudentID` = `student_terminal_rpt_fnal_student_avg`.`StudentID`)) join `student_main` on(`student_terminal_rpt_fnal_2`.`StudentID` = `student_main`.`StudentID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_fnal_3_ind`
--
DROP TABLE IF EXISTS `student_terminal_rpt_fnal_3_ind`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_fnal_3_ind`  AS SELECT `student_terminal_rpt_fnal_2_ind`.`CouponNo` AS `CouponNo`, `student_terminal_rpt_fnal_2_ind`.`Title` AS `Title`, `student_terminal_rpt_fnal_2_ind`.`StudentID` AS `StudentID`, `student_terminal_rpt_fnal_2_ind`.`FullName` AS `FullName`, `student_main_view`.`Gender` AS `Gender`, `student_terminal_rpt_fnal_2_ind`.`CourseID` AS `CourseID`, `student_terminal_rpt_fnal_2_ind`.`CourseName` AS `CourseName`, `student_terminal_rpt_fnal_2_ind`.`SubClassID` AS `SubClassID`, `student_terminal_rpt_fnal_2_ind`.`SubClassName` AS `SubClassName`, `student_terminal_rpt_fnal_2_ind`.`SubjectCatID` AS `SubjectCatID`, `student_terminal_rpt_fnal_2_ind`.`SubjectCatNm` AS `SubjectCatNm`, `student_terminal_rpt_fnal_2_ind`.`SubjectID` AS `SubjectID`, `student_terminal_rpt_fnal_2_ind`.`SubjectName` AS `SubjectName`, `student_terminal_rpt_fnal_2_ind`.`CWScore_0` AS `CWScore_0`, `student_terminal_rpt_fnal_2_ind`.`CWScore` AS `CWScore`, `student_terminal_rpt_fnal_2_ind`.`ExScore` AS `ExScore`, `student_terminal_rpt_fnal_2_ind`.`ExScore_0` AS `ExScore_0`, `student_terminal_rpt_fnal_2_ind`.`Total` AS `Total`, `student_terminal_rpt_fnal_class_avg`.`Population` AS `Population`, `student_terminal_rpt_fnal_class_avg`.`ClassTotal` AS `ClassTotal`, round(`student_terminal_rpt_fnal_class_avg`.`ClassTotal` / `student_terminal_rpt_fnal_class_avg`.`Population`,0) AS `ClassAvg`, `student_terminal_rpt_fnal_student_avg`.`SubjectCount` AS `SubjectCount`, `student_terminal_rpt_fnal_student_avg`.`GrandTotal` AS `TotalMarks`, round(`student_terminal_rpt_fnal_student_avg`.`GrandTotal` / `student_terminal_rpt_fnal_student_avg`.`SubjectCount`,0) AS `StudentAvg`, `student_terminal_rpt_fnal_2_ind`.`Username` AS `Username`, `student_terminal_rpt_fnal_2_ind`.`StaffName` AS `StaffName`, `student_terminal_rpt_fnal_2_ind`.`ViewerID` AS `ViewerID` FROM (((`student_terminal_rpt_fnal_2_ind` left join `student_terminal_rpt_fnal_class_avg` on(`student_terminal_rpt_fnal_2_ind`.`SubjectID` = `student_terminal_rpt_fnal_class_avg`.`SubjectID`)) left join `student_terminal_rpt_fnal_student_avg` on(`student_terminal_rpt_fnal_2_ind`.`StudentID` = `student_terminal_rpt_fnal_student_avg`.`StudentID`)) join `student_main_view` on(`student_terminal_rpt_fnal_2_ind`.`StudentID` = `student_main_view`.`StudentID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_fnal_class_avg`
--
DROP TABLE IF EXISTS `student_terminal_rpt_fnal_class_avg`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_fnal_class_avg`  AS SELECT `class_population`.`Poopulation` AS `Population`, round(sum(`student_terminal_rpt_fnal_2`.`Total`),2) AS `ClassTotal`, `student_terminal_rpt_fnal_2`.`SubjectID` AS `SubjectID`, `student_terminal_rpt_fnal_2`.`SubjectName` AS `SubjectName` FROM (`student_terminal_rpt_fnal_2` join `class_population` on(`student_terminal_rpt_fnal_2`.`SubClassID` = `class_population`.`CurrentClassID` and `student_terminal_rpt_fnal_2`.`CouponNo` = `class_population`.`CouponNo`)) GROUP BY `student_terminal_rpt_fnal_2`.`SubjectID` ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_fnal_details`
--
DROP TABLE IF EXISTS `student_terminal_rpt_fnal_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_fnal_details`  AS SELECT count(distinct `student_terminal_rpt_fnal_2`.`StudentID`) AS `Population`, `student_terminal_rpt_fnal_2`.`SubjectID` AS `SubjectID`, `student_terminal_rpt_fnal_2`.`SubjectName` AS `SubjectName`, max(`student_terminal_rpt_fnal_2`.`Total`) AS `ClassMax`, min(`student_terminal_rpt_fnal_2`.`Total`) AS `ClassMin`, `student_terminal_rpt_fnal_2`.`SubClassID` AS `SubClassID`, `student_terminal_rpt_fnal_2`.`SubClassName` AS `SubClassName` FROM `student_terminal_rpt_fnal_2` GROUP BY `student_terminal_rpt_fnal_2`.`SubjectID` ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_fnal_ind`
--
DROP TABLE IF EXISTS `student_terminal_rpt_fnal_ind`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_fnal_ind`  AS SELECT `student_terminal_rpt_names_0_ind`.`CouponNo` AS `CouponNo`, `student_terminal_rpt_names_0_ind`.`Title` AS `Title`, `student_terminal_rpt_names_0_ind`.`StudentID` AS `StudentID`, `student_terminal_rpt_names_0_ind`.`FullName` AS `FullName`, `student_terminal_rpt_names_0_ind`.`CourseID` AS `CourseID`, `student_terminal_rpt_names_0_ind`.`CourseName` AS `CourseName`, `student_terminal_rpt_names_0_ind`.`SubClassID` AS `SubClassID`, `student_terminal_rpt_names_0_ind`.`SubClassName` AS `SubClassName`, `student_terminal_rpt_names_0_ind`.`SubjectID` AS `SubjectID`, `student_terminal_rpt_names_0_ind`.`SubjectName` AS `SubjectName`, CASE WHEN `student_terminal_rpt_class_test_1`.`Percentage` is null THEN '0' ELSE `student_terminal_rpt_class_test_1`.`Percentage` END AS `CWPcntg`, CASE WHEN `student_terminal_rpt_class_test_1`.`Score` is null THEN 0 ELSE `student_terminal_rpt_class_test_1`.`Score` END AS `CWScore_0`, CASE WHEN `student_terminal_rpt_class_test_1`.`CWScore` is null THEN 0 ELSE `student_terminal_rpt_class_test_1`.`CWScore` END AS `CWScore`, `student_terminal_rpt_names_0_ind`.`Username` AS `Username`, `student_terminal_rpt_names_0_ind`.`ViewerID` AS `ViewerID` FROM (`student_terminal_rpt_names_0_ind` left join `student_terminal_rpt_class_test_1` on(`student_terminal_rpt_names_0_ind`.`StudentID` = `student_terminal_rpt_class_test_1`.`StudentID` and `student_terminal_rpt_names_0_ind`.`SubjectID` = `student_terminal_rpt_class_test_1`.`SubjectID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_fnal_student_avg`
--
DROP TABLE IF EXISTS `student_terminal_rpt_fnal_student_avg`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_fnal_student_avg`  AS SELECT DISTINCT `student_terminal_rpt_fnal_2`.`StudentID` AS `StudentID`, round(sum(`student_terminal_rpt_fnal_2`.`Total`),2) AS `GrandTotal`, count(distinct `student_terminal_rpt_fnal_2`.`SubjectID`) AS `SubjectCount` FROM `student_terminal_rpt_fnal_2` GROUP BY `student_terminal_rpt_fnal_2`.`StudentID` ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_ind`
--
DROP TABLE IF EXISTS `student_terminal_rpt_ind`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_ind`  AS SELECT `student_test_score_view`.`CouponNo` AS `CouponNo`, `student_test_score_view`.`Title` AS `Title`, `student_test_score_view`.`StudentID` AS `StudentID`, `student_test_score_view`.`FullName` AS `FullName`, `student_test_score_view`.`CourseID` AS `CourseID`, `student_test_score_view`.`CourseName` AS `CourseName`, `student_test_score_view`.`SubClassID` AS `SubClassID`, `student_test_score_view`.`SubClassName` AS `SubClassName`, `student_test_score_view`.`TestType` AS `TestType`, `student_test_score_view`.`TestID` AS `TestID`, `student_test_score_view`.`SubjectID` AS `SubjectID`, `student_test_score_view`.`SubjectName` AS `SubjectName`, `student_test_score_view`.`Score` AS `Score`, `student_test_score_view`.`Username` AS `Username`, `student_test_score_view`.`FName` AS `FName`, `student_test_score_view`.`StaffName` AS `StaffName`, `student_test_score_view`.`Date` AS `Date`, `student_test_score_view`.`Time` AS `Time`, `student_test_score_view`.`Status` AS `Status`, `rpt_multi_values_0`.`Username` AS `ViewerID` FROM (`student_test_score_view` join `rpt_multi_values_0`) WHERE `student_test_score_view`.`CouponNo` = `rpt_multi_values_0`.`Value2` AND `student_test_score_view`.`StudentID` = `rpt_multi_values_0`.`Value1` ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_names`
--
DROP TABLE IF EXISTS `student_terminal_rpt_names`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_names`  AS SELECT `student_terminal_rpt`.`CouponNo` AS `CouponNo`, `student_terminal_rpt`.`Title` AS `Title`, `student_terminal_rpt`.`StudentID` AS `StudentID`, `student_terminal_rpt`.`FullName` AS `FullName`, `student_terminal_rpt`.`CourseID` AS `CourseID`, `student_terminal_rpt`.`CourseName` AS `CourseName`, `student_terminal_rpt`.`SubClassID` AS `SubClassID`, `student_terminal_rpt`.`SubClassName` AS `SubClassName`, `test_setup`.`Type` AS `Category`, `student_terminal_rpt`.`TestID` AS `TestID`, `student_terminal_rpt`.`TestType` AS `TestType`, `student_terminal_rpt`.`SubjectID` AS `SubjectID`, `student_terminal_rpt`.`SubjectName` AS `SubjectName`, `student_terminal_rpt`.`Score` AS `Score`, `student_terminal_rpt`.`Username` AS `Username`, `student_terminal_rpt`.`ViewerID` AS `ViewerID` FROM (`student_terminal_rpt` join `test_setup` on(`student_terminal_rpt`.`TestID` = `test_setup`.`TestID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_names_0`
--
DROP TABLE IF EXISTS `student_terminal_rpt_names_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_names_0`  AS SELECT DISTINCT `student_terminal_rpt_names`.`CouponNo` AS `CouponNo`, `student_terminal_rpt_names`.`Title` AS `Title`, `student_terminal_rpt_names`.`StudentID` AS `StudentID`, `student_terminal_rpt_names`.`FullName` AS `FullName`, `student_terminal_rpt_names`.`CourseID` AS `CourseID`, `student_terminal_rpt_names`.`CourseName` AS `CourseName`, `student_terminal_rpt_names`.`SubClassID` AS `SubClassID`, `student_terminal_rpt_names`.`SubClassName` AS `SubClassName`, `student_terminal_rpt_names`.`SubjectID` AS `SubjectID`, `student_terminal_rpt_names`.`SubjectName` AS `SubjectName`, `student_terminal_rpt_names`.`Username` AS `Username`, `student_terminal_rpt_names`.`ViewerID` AS `ViewerID` FROM `student_terminal_rpt_names` ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_names_0_ind`
--
DROP TABLE IF EXISTS `student_terminal_rpt_names_0_ind`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_names_0_ind`  AS SELECT DISTINCT `student_terminal_rpt_names_ind`.`CouponNo` AS `CouponNo`, `student_terminal_rpt_names_ind`.`Title` AS `Title`, `student_terminal_rpt_names_ind`.`StudentID` AS `StudentID`, `student_terminal_rpt_names_ind`.`FullName` AS `FullName`, `student_terminal_rpt_names_ind`.`CourseID` AS `CourseID`, `student_terminal_rpt_names_ind`.`CourseName` AS `CourseName`, `student_terminal_rpt_names_ind`.`SubClassID` AS `SubClassID`, `student_terminal_rpt_names_ind`.`SubClassName` AS `SubClassName`, `student_terminal_rpt_names_ind`.`SubjectID` AS `SubjectID`, `student_terminal_rpt_names_ind`.`SubjectName` AS `SubjectName`, `student_terminal_rpt_names_ind`.`Username` AS `Username`, `student_terminal_rpt_names_ind`.`ViewerID` AS `ViewerID` FROM `student_terminal_rpt_names_ind` ;

-- --------------------------------------------------------

--
-- Structure for view `student_terminal_rpt_names_ind`
--
DROP TABLE IF EXISTS `student_terminal_rpt_names_ind`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_terminal_rpt_names_ind`  AS SELECT `student_terminal_rpt_ind`.`CouponNo` AS `CouponNo`, `student_terminal_rpt_ind`.`Title` AS `Title`, `student_terminal_rpt_ind`.`StudentID` AS `StudentID`, `student_terminal_rpt_ind`.`FullName` AS `FullName`, `student_terminal_rpt_ind`.`CourseID` AS `CourseID`, `student_terminal_rpt_ind`.`CourseName` AS `CourseName`, `student_terminal_rpt_ind`.`SubClassID` AS `SubClassID`, `student_terminal_rpt_ind`.`SubClassName` AS `SubClassName`, `test_setup`.`Type` AS `Category`, `student_terminal_rpt_ind`.`TestID` AS `TestID`, `student_terminal_rpt_ind`.`TestType` AS `TestType`, `student_terminal_rpt_ind`.`SubjectID` AS `SubjectID`, `student_terminal_rpt_ind`.`SubjectName` AS `SubjectName`, `student_terminal_rpt_ind`.`Score` AS `Score`, `student_terminal_rpt_ind`.`Username` AS `Username`, `student_terminal_rpt_ind`.`ViewerID` AS `ViewerID` FROM (`student_terminal_rpt_ind` join `test_setup` on(`student_terminal_rpt_ind`.`TestID` = `test_setup`.`TestID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_test_score_view`
--
DROP TABLE IF EXISTS `student_test_score_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_test_score_view`  AS SELECT `student_test_score`.`CouponNo` AS `CouponNo`, `academic_coupon`.`Title` AS `Title`, `student_test_score`.`StudentID` AS `StudentID`, `student_current_class`.`FullName` AS `FullName`, `sub_class_main_view`.`CourseID` AS `CourseID`, `sub_class_main_view`.`ClassName` AS `CourseName`, `student_test_score`.`SubClassID` AS `SubClassID`, `sub_class_main_view`.`SubClassName` AS `SubClassName`, `test_setup`.`Type` AS `TestCategory`, `test_setup`.`TestName` AS `TestType`, `student_test_score`.`TestID` AS `TestID`, `student_test_score`.`SubjectID` AS `SubjectID`, `subject_main_view`.`SubjectName` AS `SubjectName`, `student_test_score`.`Score` AS `Score`, `student_test_score`.`Username` AS `Username`, `kaina`.`FullName` AS `FName`, `staff_main_view`.`FullName` AS `StaffName`, `student_test_score`.`Date` AS `Date`, `student_test_score`.`Time` AS `Time`, `student_test_score`.`Status` AS `Status` FROM (((((((`student_test_score` join `student_current_class` on(`student_test_score`.`StudentID` = `student_current_class`.`StudentID`)) join `sub_class_main_view` on(`student_test_score`.`SubClassID` = `sub_class_main_view`.`SubClassID`)) join `staff_main_view` on(`student_test_score`.`Username` = `staff_main_view`.`StaffID`)) join `subject_main_view` on(`student_test_score`.`SubjectID` = `subject_main_view`.`SubjectID`)) join `academic_coupon` on(`student_test_score`.`CouponNo` = `academic_coupon`.`CouponNo`)) join `kaina` on(`student_test_score`.`Username` = `kaina`.`ID`)) join `test_setup` on(`student_test_score`.`TestID` = `test_setup`.`TestID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `student_write_off_fee_balance`
--
DROP TABLE IF EXISTS `student_write_off_fee_balance`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_write_off_fee_balance`  AS SELECT `student_fee`.`StudentID` AS `StudentID`, `student_fee`.`SubClassID` AS `SubClassID`, `student_fee`.`AccountNo` AS `AccountNo`, `student_fee`.`Stamp` AS `Stamp`, `student_fee`.`CouponID` AS `CouponID`, `student_fee`.`Description` AS `Description`, `student_fee`.`ReceiptNo` AS `ReceiptNo`, `student_fee`.`Dr` AS `Dr`, `student_fee`.`Cr` AS `Cr`, `student_fee`.`Date` AS `Date`, `t_date`.`ActiveDate` AS `ActiveDate`, `student_fee`.`Time` AS `Time`, `student_fee`.`Username` AS `Username`, `student_fee`.`Status` AS `Status` FROM (`student_fee` join `t_date`) WHERE `student_fee`.`Status` = '1' AND `student_fee`.`Date` <= `t_date`.`ActiveDate` ;

-- --------------------------------------------------------

--
-- Structure for view `student_write_off_fee_balance_0`
--
DROP TABLE IF EXISTS `student_write_off_fee_balance_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_write_off_fee_balance_0`  AS SELECT `student_write_off_fee_balance`.`StudentID` AS `StudentID`, `student_write_off_fee_balance`.`Stamp` AS `Stamp`, round(sum(`student_write_off_fee_balance`.`Dr`),2) AS `TDr`, round(sum(`student_write_off_fee_balance`.`Cr`),2) AS `TCr`, round(sum(`student_write_off_fee_balance`.`Dr`) - sum(`student_write_off_fee_balance`.`Cr`),2) AS `Balance`, `student_write_off_fee_balance`.`ActiveDate` AS `ActiveDate` FROM `student_write_off_fee_balance` GROUP BY `student_write_off_fee_balance`.`StudentID`, `student_write_off_fee_balance`.`Stamp` ;

-- --------------------------------------------------------

--
-- Structure for view `student_write_off_fee_balance_1`
--
DROP TABLE IF EXISTS `student_write_off_fee_balance_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_write_off_fee_balance_1`  AS SELECT `student_write_off_fee_balance_0`.`StudentID` AS `StudentID`, `active_students`.`FullName` AS `FullName`, `active_students`.`CurrentClassID` AS `CurrentClassID`, `active_students`.`CurrentClassName` AS `CurrentClassName`, `student_write_off_fee_balance_0`.`Stamp` AS `Stamp`, `student_write_off_fee_balance_0`.`TDr` AS `TDr`, `student_write_off_fee_balance_0`.`TCr` AS `TCr`, `student_write_off_fee_balance_0`.`Balance` AS `Balance`, `student_write_off_fee_balance_0`.`ActiveDate` AS `ActiveDate` FROM (`student_write_off_fee_balance_0` join `active_students` on(`student_write_off_fee_balance_0`.`StudentID` = `active_students`.`StudentID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `subclass_form_master_view`
--
DROP TABLE IF EXISTS `subclass_form_master_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `subclass_form_master_view`  AS SELECT `subclass_form_master`.`StaffID` AS `StaffID`, `staff_main`.`FullName` AS `FullName`, `subclass_form_master`.`SubClassID` AS `SubClassID`, `sub_class_main`.`SubClassName` AS `SubClassName`, `subclass_form_master`.`Date` AS `Date`, `subclass_form_master`.`Time` AS `Time`, `subclass_form_master`.`Username` AS `Username`, `subclass_form_master`.`Status` AS `Status` FROM ((`subclass_form_master` join `staff_main` on(`subclass_form_master`.`StaffID` = `staff_main`.`StaffID`)) join `sub_class_main` on(`subclass_form_master`.`SubClassID` = `sub_class_main`.`SubClassID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `subject_main_view`
--
DROP TABLE IF EXISTS `subject_main_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `subject_main_view`  AS SELECT `subject_main`.`CategoryID` AS `CategoryID`, `subj_category`.`CategoryName` AS `CategoryName`, `subject_main`.`SubjectID` AS `SubjectID`, `subject_main`.`SubjectName` AS `SubjectName`, `subject_main`.`Date` AS `Date`, `subject_main`.`Time` AS `Time`, `subject_main`.`Username` AS `Username`, `subject_main`.`Status` AS `Status` FROM (`subject_main` join `subj_category` on(`subject_main`.`CategoryID` = `subj_category`.`CategoryID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `sub_class_id_view`
--
DROP TABLE IF EXISTS `sub_class_id_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sub_class_id_view`  AS SELECT `sub_class_subject_view`.`SubClassID` AS `SubClassID`, `sub_class_subject_view`.`SubClassName` AS `SubClassName`, `sub_class_subject_view`.`CategoryID` AS `CategoryID`, `sub_class_subject_view`.`CategoryName` AS `CategoryName`, `sub_class_subject_view`.`SubjectID` AS `SubjectID`, `sub_class_subject_view`.`SubjectName` AS `SubjectName`, `sub_class_id`.`Username` AS `Username`, `sub_class_subject_view`.`Time` AS `Time`, `sub_class_subject_view`.`Status` AS `Status` FROM (`sub_class_subject_view` join `sub_class_id`) WHERE `sub_class_subject_view`.`SubClassID` = `sub_class_id`.`SubClassID` ;

-- --------------------------------------------------------

--
-- Structure for view `sub_class_main_view`
--
DROP TABLE IF EXISTS `sub_class_main_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sub_class_main_view`  AS SELECT `sub_class_main`.`CourseID` AS `CourseID`, `class_main`.`ClassName` AS `ClassName`, `sub_class_main`.`SubClassID` AS `SubClassID`, `sub_class_main`.`SubClassName` AS `SubClassName`, `sub_class_main`.`Enroll` AS `Enroll`, `sub_class_main`.`Date` AS `Date`, `sub_class_main`.`Time` AS `Time`, `sub_class_main`.`Username` AS `Username`, `sub_class_main`.`Status` AS `Status` FROM (`sub_class_main` join `class_main` on(`sub_class_main`.`CourseID` = `class_main`.`ClassID`)) WHERE `class_main`.`Status` = 1 ;

-- --------------------------------------------------------

--
-- Structure for view `sub_class_subject_view`
--
DROP TABLE IF EXISTS `sub_class_subject_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sub_class_subject_view`  AS SELECT `sub_class_subject`.`SubClassID` AS `SubClassID`, `sub_class_main`.`SubClassName` AS `SubClassName`, `subject_main_view`.`CategoryID` AS `CategoryID`, `subject_main_view`.`CategoryName` AS `CategoryName`, `sub_class_subject`.`SubjectID` AS `SubjectID`, `subject_main_view`.`SubjectName` AS `SubjectName`, `sub_class_subject`.`Username` AS `Username`, `sub_class_subject`.`Time` AS `Time`, `sub_class_subject`.`Status` AS `Status` FROM ((`sub_class_subject` join `sub_class_main` on(`sub_class_subject`.`SubClassID` = `sub_class_main`.`SubClassID`)) join `subject_main_view` on(`sub_class_subject`.`SubjectID` = `subject_main_view`.`SubjectID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `temp_class_subj_map_view`
--
DROP TABLE IF EXISTS `temp_class_subj_map_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `temp_class_subj_map_view`  AS SELECT `temp_class_subj_map`.`CategoryID` AS `CategoryID`, `subj_category`.`CategoryName` AS `CategoryName`, `temp_class_subj_map`.`SubjectID` AS `SubjectID`, `subject_main`.`SubjectName` AS `SubjectName`, `temp_class_subj_map`.`Username` AS `Username`, `temp_class_subj_map`.`Time` AS `Time` FROM ((`temp_class_subj_map` join `subj_category` on(`temp_class_subj_map`.`CategoryID` = `subj_category`.`CategoryID`)) join `subject_main` on(`temp_class_subj_map`.`SubjectID` = `subject_main`.`SubjectID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `temp_general_student_billing_view`
--
DROP TABLE IF EXISTS `temp_general_student_billing_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `temp_general_student_billing_view`  AS SELECT `temp_general_student_billing`.`StudentID` AS `StudentID`, `temp_general_student_billing`.`FullName` AS `FullName`, `temp_general_student_billing`.`SubClassID` AS `SubClassID`, `temp_general_student_billing`.`SubClassName` AS `SubClassName`, `map_school_fee_view`.`AccountID` AS `AccountID`, `map_school_fee_view`.`AccountName` AS `AccountName`, `map_school_fee_view`.`Amount` AS `Amount`, `temp_general_student_billing`.`Status` AS `Status`, `temp_general_student_billing`.`Username` AS `Username`, `temp_general_student_billing`.`Date` AS `Date`, `temp_general_student_billing`.`Time` AS `Time`, `temp_general_student_billing`.`BranchID` AS `BranchID` FROM (`temp_general_student_billing` join `map_school_fee_view` on(`temp_general_student_billing`.`SubClassID` = `map_school_fee_view`.`SubClassID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `temp_general_student_billing_view_0`
--
DROP TABLE IF EXISTS `temp_general_student_billing_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `temp_general_student_billing_view_0`  AS SELECT `temp_general_student_billing_view`.`StudentID` AS `StudentID`, `temp_general_student_billing_view`.`FullName` AS `FullName`, `temp_general_student_billing_view`.`SubClassID` AS `SubClassID`, `temp_general_student_billing_view`.`SubClassName` AS `SubClassName`, `temp_general_student_billing_view`.`AccountID` AS `AccountID`, `temp_general_student_billing_view`.`AccountName` AS `AccountName`, `temp_general_student_billing_view`.`Amount` AS `Amount`, `temp_general_student_billing_view`.`Status` AS `Status`, `temp_general_student_billing_view`.`Username` AS `Username`, `temp_general_student_billing_view`.`Date` AS `Date`, `temp_general_student_billing_view`.`Time` AS `Time`, `temp_general_student_billing_view`.`BranchID` AS `BranchID` FROM `temp_general_student_billing_view` ;

-- --------------------------------------------------------

--
-- Structure for view `temp_general_student_billing_view_1`
--
DROP TABLE IF EXISTS `temp_general_student_billing_view_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `temp_general_student_billing_view_1`  AS SELECT `temp_general_student_billing_view_0`.`StudentID` AS `StudentID`, `temp_general_student_billing_view_0`.`FullName` AS `FullName`, `temp_general_student_billing_view_0`.`SubClassID` AS `SubClassID`, `temp_general_student_billing_view_0`.`SubClassName` AS `SubClassName`, `temp_general_student_billing_view_0`.`AccountID` AS `AccountID`, `temp_general_student_billing_view_0`.`AccountName` AS `AccountName`, round(sum(`temp_general_student_billing_view_0`.`Amount`),2) AS `Amount`, `temp_general_student_billing_view_0`.`Status` AS `Status`, `temp_general_student_billing_view_0`.`Username` AS `Username`, `temp_general_student_billing_view_0`.`Date` AS `Date`, `temp_general_student_billing_view_0`.`Time` AS `Time`, `temp_general_student_billing_view_0`.`BranchID` AS `BranchID` FROM `temp_general_student_billing_view_0` GROUP BY `temp_general_student_billing_view_0`.`StudentID` ;

-- --------------------------------------------------------

--
-- Structure for view `temp_mainbl_new_consignee`
--
DROP TABLE IF EXISTS `temp_mainbl_new_consignee`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `temp_mainbl_new_consignee`  AS SELECT `rpt_multi_values_0`.`Value1` AS `ConsigenmentID`, `container_main_view`.`ShipperID` AS `ShipperID`, `container_main_view`.`ShipperName` AS `ShipperName`, `container_main_view`.`VesselName` AS `VesselName`, round(`container_main_view`.`ContWeight`,2) AS `ContWeight`, `container_main_view`.`ContainerNo` AS `ContainerNo`, `container_main_view`.`ContainerSize` AS `ContainerSize`, `rpt_multi_values_0`.`Value2` AS `MainBL`, `rpt_multi_values_0`.`Username` AS `Username`, `rpt_multi_values_0`.`Time` AS `Time` FROM (`rpt_multi_values_0` join `container_main_view` on(`rpt_multi_values_0`.`Value1` = `container_main_view`.`ConsignmentID` and `rpt_multi_values_0`.`Value2` = `container_main_view`.`BL` and `rpt_multi_values_0`.`Value3` = `container_main_view`.`ContainerNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `temp_manifestation_breakdown_total_weight_view`
--
DROP TABLE IF EXISTS `temp_manifestation_breakdown_total_weight_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `temp_manifestation_breakdown_total_weight_view`  AS SELECT `temp_manifestation_breakdown_view`.`ConsignmentID` AS `ConsignmentID`, `temp_manifestation_breakdown_view`.`MainBL` AS `MainBL`, round(sum(`temp_manifestation_breakdown_view`.`Weight`),3) AS `TWeight`, `temp_manifestation_breakdown_view`.`Username` AS `Username`, `temp_mainbl_new_consignee`.`ContWeight` AS `ContWeight` FROM (`temp_manifestation_breakdown_view` join `temp_mainbl_new_consignee` on(`temp_manifestation_breakdown_view`.`ConsignmentID` = `temp_mainbl_new_consignee`.`ConsigenmentID` and `temp_manifestation_breakdown_view`.`Username` = `temp_mainbl_new_consignee`.`Username` and `temp_manifestation_breakdown_view`.`MainBL` = `temp_mainbl_new_consignee`.`MainBL`)) GROUP BY `temp_manifestation_breakdown_view`.`Username`, `temp_manifestation_breakdown_view`.`ConsignmentID`, `temp_manifestation_breakdown_view`.`MainBL` ;

-- --------------------------------------------------------

--
-- Structure for view `temp_manifestation_breakdown_total_weight_view_0`
--
DROP TABLE IF EXISTS `temp_manifestation_breakdown_total_weight_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `temp_manifestation_breakdown_total_weight_view_0`  AS SELECT `temp_manifestation_breakdown_total_weight_view`.`ConsignmentID` AS `ConsignmentID`, `temp_manifestation_breakdown_total_weight_view`.`MainBL` AS `MainBL`, `temp_manifestation_breakdown_total_weight_view`.`TWeight` AS `TWeight`, `temp_manifestation_breakdown_total_weight_view`.`Username` AS `Username`, `temp_manifestation_breakdown_total_weight_view`.`ContWeight` AS `ContWeight`, `container_main_view_total_weight`.`BLWeight` AS `BLWeight`, round(`container_main_view_total_weight`.`BLWeight` - `temp_manifestation_breakdown_total_weight_view`.`TWeight`,2) AS `RemWieght` FROM (`temp_manifestation_breakdown_total_weight_view` left join `container_main_view_total_weight` on(`temp_manifestation_breakdown_total_weight_view`.`MainBL` = `container_main_view_total_weight`.`BL` and `temp_manifestation_breakdown_total_weight_view`.`Username` = `container_main_view_total_weight`.`Username`)) ;

-- --------------------------------------------------------

--
-- Structure for view `temp_manifestation_breakdown_view`
--
DROP TABLE IF EXISTS `temp_manifestation_breakdown_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `temp_manifestation_breakdown_view`  AS SELECT `temp_manifestation_breakdown`.`ConsignmentID` AS `ConsignmentID`, `temp_manifestation_breakdown`.`MainBL` AS `MainBL`, `temp_manifestation_breakdown`.`HouseBL` AS `HouseBL`, `temp_manifestation_breakdown`.`ContainerNo` AS `ContainerNo`, right(`temp_manifestation_breakdown`.`MainBL`,4) AS `HBLNo`, right(`temp_manifestation_breakdown`.`HouseBL`,1) AS `HDLID`, `temp_manifestation_breakdown`.`CosigneeID` AS `CosigneeID`, `consignee_main`.`FullName` AS `FullName`, `temp_manifestation_breakdown`.`Cosignee2_ID` AS `Cosignee2_ID`, `temp_manifestation_breakdown`.`Weight` AS `Weight`, `temp_manifestation_breakdown`.`Package` AS `Package`, `temp_manifestation_breakdown`.`Description` AS `Description`, `temp_manifestation_breakdown`.`ItemType` AS `ItemType`, `temp_manifestation_breakdown`.`VIN` AS `VIN`, `temp_manifestation_breakdown`.`OtherInfo` AS `OtherInfo`, `temp_manifestation_breakdown`.`Unit` AS `Unit`, `temp_manifestation_breakdown`.`Username` AS `Username`, `temp_manifestation_breakdown`.`Time` AS `Time` FROM (`temp_manifestation_breakdown` join `consignee_main` on(`temp_manifestation_breakdown`.`CosigneeID` = `consignee_main`.`ConsigneeID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `temp_manifestation_breakdown_view_0`
--
DROP TABLE IF EXISTS `temp_manifestation_breakdown_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `temp_manifestation_breakdown_view_0`  AS SELECT `temp_manifestation_breakdown_view`.`ConsignmentID` AS `ConsignmentID`, `temp_manifestation_breakdown_view`.`MainBL` AS `MainBL`, `temp_manifestation_breakdown_view`.`ContainerNo` AS `ContainerNo`, round(sum(`temp_manifestation_breakdown_view`.`Weight`),2) AS `TWeight`, `temp_manifestation_breakdown_view`.`Username` AS `Username`, `temp_manifestation_breakdown_view`.`Time` AS `time` FROM `temp_manifestation_breakdown_view` GROUP BY `temp_manifestation_breakdown_view`.`MainBL`, `temp_manifestation_breakdown_view`.`ContainerNo`, `temp_manifestation_breakdown_view`.`Username` ;

-- --------------------------------------------------------

--
-- Structure for view `temp_other_invoice_non_manifest_view`
--
DROP TABLE IF EXISTS `temp_other_invoice_non_manifest_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `temp_other_invoice_non_manifest_view`  AS SELECT `temp_other_invoice_non_manifest`.`ClientID` AS `ClientID`, `temp_other_invoice_non_manifest`.`AccountNo` AS `AccountNo`, `ledger_account`.`AccountName` AS `AccountName`, `temp_other_invoice_non_manifest`.`Amount` AS `Amount`, `temp_other_invoice_non_manifest`.`TaxStatus` AS `TaxStatus`, `temp_other_invoice_non_manifest`.`GetFund` AS `GetFund`, `temp_other_invoice_non_manifest`.`NHIL` AS `NHIL`, `temp_other_invoice_non_manifest`.`Covid` AS `Covid`, round(`temp_other_invoice_non_manifest`.`GetFund` * `temp_other_invoice_non_manifest`.`Amount`,2) AS `GetFundVal`, round(`temp_other_invoice_non_manifest`.`NHIL` * `temp_other_invoice_non_manifest`.`Amount`,2) AS `NHILVal`, round(`temp_other_invoice_non_manifest`.`Covid` * `temp_other_invoice_non_manifest`.`Amount`,2) AS `CovidVal`, round(`temp_other_invoice_non_manifest`.`GetFund` * `temp_other_invoice_non_manifest`.`Amount` + `temp_other_invoice_non_manifest`.`NHIL` * `temp_other_invoice_non_manifest`.`Amount` + `temp_other_invoice_non_manifest`.`Covid` * `temp_other_invoice_non_manifest`.`Amount` + `temp_other_invoice_non_manifest`.`Amount`,2) AS `SubTotal`, `temp_other_invoice_non_manifest`.`VAT` AS `VAT`, `temp_other_invoice_non_manifest`.`Username` AS `Username`, `temp_other_invoice_non_manifest`.`Time` AS `Time` FROM (`temp_other_invoice_non_manifest` join `ledger_account` on(`temp_other_invoice_non_manifest`.`AccountNo` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `temp_other_invoice_non_manifest_view_0`
--
DROP TABLE IF EXISTS `temp_other_invoice_non_manifest_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `temp_other_invoice_non_manifest_view_0`  AS SELECT `temp_other_invoice_non_manifest_view`.`ClientID` AS `ClientID`, `temp_other_invoice_non_manifest_view`.`AccountNo` AS `AccountNo`, `temp_other_invoice_non_manifest_view`.`AccountName` AS `AccountName`, `temp_other_invoice_non_manifest_view`.`Amount` AS `Amount`, `temp_other_invoice_non_manifest_view`.`TaxStatus` AS `TaxStatus`, `temp_other_invoice_non_manifest_view`.`GetFund` AS `GetFund`, `temp_other_invoice_non_manifest_view`.`GetFundVal` AS `GetFundVal`, `temp_other_invoice_non_manifest_view`.`SubTotal` AS `SubTotal`, `temp_other_invoice_non_manifest_view`.`VAT` AS `VAT`, round(`temp_other_invoice_non_manifest_view`.`VAT` * `temp_other_invoice_non_manifest_view`.`SubTotal`,2) AS `VATVal`, round(`temp_other_invoice_non_manifest_view`.`SubTotal` * `temp_other_invoice_non_manifest_view`.`VAT` + `temp_other_invoice_non_manifest_view`.`Amount` + `temp_other_invoice_non_manifest_view`.`GetFundVal`,2) AS `GTotal`, `temp_other_invoice_non_manifest_view`.`Username` AS `Username`, `temp_other_invoice_non_manifest_view`.`Time` AS `Time` FROM `temp_other_invoice_non_manifest_view` WHERE 1 ;

-- --------------------------------------------------------

--
-- Structure for view `temp_other_invoice_view`
--
DROP TABLE IF EXISTS `temp_other_invoice_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `temp_other_invoice_view`  AS SELECT `temp_other_invoice`.`ClientID` AS `ClientID`, `temp_other_invoice`.`AccountNo` AS `AccountNo`, `ledger_account`.`AccountName` AS `AccountName`, `temp_other_invoice`.`Amount` AS `Amount`, `temp_other_invoice`.`Description` AS `Description`, `temp_other_invoice`.`GetFund` AS `GetFund`, round(`temp_other_invoice`.`GetFund` * `temp_other_invoice`.`Amount`,2) AS `GetFundVal`, round(`temp_other_invoice`.`GetFund` * `temp_other_invoice`.`Amount` + `temp_other_invoice`.`Amount`,2) AS `SubTotal`, `temp_other_invoice`.`VAT` AS `VAT`, `temp_other_invoice`.`Username` AS `Username`, `temp_other_invoice`.`Time` AS `Time` FROM (`temp_other_invoice` join `ledger_account` on(`temp_other_invoice`.`AccountNo` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `temp_other_invoice_view_0`
--
DROP TABLE IF EXISTS `temp_other_invoice_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `temp_other_invoice_view_0`  AS SELECT `temp_other_invoice_view`.`ClientID` AS `ClientID`, `temp_other_invoice_view`.`AccountNo` AS `AccountNo`, `temp_other_invoice_view`.`AccountName` AS `AccountName`, `temp_other_invoice_view`.`Amount` AS `Amount`, `temp_other_invoice_view`.`Description` AS `Description`, `temp_other_invoice_view`.`GetFund` AS `GetFund`, `temp_other_invoice_view`.`GetFundVal` AS `GetFundVal`, `temp_other_invoice_view`.`SubTotal` AS `SubTotal`, `temp_other_invoice_view`.`VAT` AS `VAT`, round(`temp_other_invoice_view`.`VAT` * `temp_other_invoice_view`.`SubTotal`,2) AS `VATVal`, round(`temp_other_invoice_view`.`SubTotal` * `temp_other_invoice_view`.`VAT` + `temp_other_invoice_view`.`Amount` + `temp_other_invoice_view`.`GetFundVal`,2) AS `GTotal`, `temp_other_invoice_view`.`Username` AS `Username`, `temp_other_invoice_view`.`Time` AS `Time` FROM `temp_other_invoice_view` WHERE 1 ;

-- --------------------------------------------------------

--
-- Structure for view `temp_staff_class_subj_mapp_view`
--
DROP TABLE IF EXISTS `temp_staff_class_subj_mapp_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `temp_staff_class_subj_mapp_view`  AS SELECT `temp_staff_class_subj_mapp`.`StaffID` AS `StaffID`, `staff_main`.`FullName` AS `FullName`, `temp_staff_class_subj_mapp`.`ClassID` AS `ClassID`, `sub_class_main`.`SubClassName` AS `SubClassName`, `temp_staff_class_subj_mapp`.`SubjectID` AS `SubjectID`, `subject_main_view`.`SubjectName` AS `SubjectName`, `subject_main_view`.`CategoryName` AS `CategoryName`, `temp_staff_class_subj_mapp`.`Time` AS `Time`, `temp_staff_class_subj_mapp`.`Username` AS `Username` FROM (((`temp_staff_class_subj_mapp` join `sub_class_main` on(`temp_staff_class_subj_mapp`.`ClassID` = `sub_class_main`.`SubClassID`)) join `subject_main_view` on(`temp_staff_class_subj_mapp`.`SubjectID` = `subject_main_view`.`SubjectID`)) join `staff_main` on(`temp_staff_class_subj_mapp`.`StaffID` = `staff_main`.`StaffID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `temp_student_register_view`
--
DROP TABLE IF EXISTS `temp_student_register_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `temp_student_register_view`  AS SELECT `temp_student_register`.`StudentID` AS `StudentID`, `student_current_class`.`FullName` AS `FullName`, `temp_student_register`.`SubClassID` AS `SubClassID`, `student_current_class`.`SubCurrentClassID` AS `SubCurrentClassID`, `student_current_class`.`SubCurrentClassName` AS `SubCurrentClassName`, `temp_student_register`.`Attendance` AS `Attendance`, `temp_student_register`.`Username` AS `Username`, `temp_student_register`.`Date` AS `Date`, `temp_student_register`.`Time` AS `Time` FROM (`temp_student_register` join `student_current_class` on(`temp_student_register`.`StudentID` = `student_current_class`.`StudentID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `temp_student_test_score_view`
--
DROP TABLE IF EXISTS `temp_student_test_score_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `temp_student_test_score_view`  AS SELECT `temp_student_test_score`.`StudentID` AS `StudentID`, concat(`student_main`.`FirstName`,' ',`student_main`.`LastName`) AS `FullName`, `temp_student_test_score`.`SubClassID` AS `SubClassID`, `sub_class_main`.`SubClassName` AS `SubClassName`, `temp_student_test_score`.`SubjectID` AS `SubjectID`, `subject_main`.`SubjectName` AS `SubjectName`, `temp_student_test_score`.`TestID` AS `TestID`, `test_setup`.`TestName` AS `TestName`, `test_setup`.`MaxScore` AS `MaxScore`, `temp_student_test_score`.`Score` AS `Score`, `temp_student_test_score`.`Username` AS `Username`, `temp_student_test_score`.`Time` AS `Time` FROM ((((`temp_student_test_score` join `student_main` on(`temp_student_test_score`.`StudentID` = `student_main`.`StudentID`)) join `sub_class_main` on(`temp_student_test_score`.`SubClassID` = `sub_class_main`.`SubClassID`)) join `subject_main` on(`temp_student_test_score`.`SubjectID` = `subject_main`.`SubjectID`)) join `test_setup` on(`temp_student_test_score`.`TestID` = `test_setup`.`TestID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `temp_transaction_reversal_view`
--
DROP TABLE IF EXISTS `temp_transaction_reversal_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `temp_transaction_reversal_view`  AS SELECT `temp_transaction_reversal`.`AccountID` AS `AccountID`, `ledger_account`.`AccountName` AS `AccountName`, `temp_transaction_reversal`.`SubAccountID` AS `SubAccountID`, `temp_transaction_reversal`.`Mode` AS `Mode`, `temp_transaction_reversal`.`ReceiptNo` AS `ReceiptNo`, `temp_transaction_reversal`.`Dr` AS `Dr`, `temp_transaction_reversal`.`Cr` AS `Cr`, `temp_transaction_reversal`.`Date` AS `Date`, `temp_transaction_reversal`.`Time` AS `Time`, `temp_transaction_reversal`.`Username` AS `Username` FROM (`temp_transaction_reversal` join `ledger_account` on(`temp_transaction_reversal`.`AccountID` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `temp_transaction_reversal_view_0`
--
DROP TABLE IF EXISTS `temp_transaction_reversal_view_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `temp_transaction_reversal_view_0`  AS SELECT `temp_transaction_reversal_view`.`AccountID` AS `AccountID`, `temp_transaction_reversal_view`.`AccountName` AS `AccountName`, `temp_transaction_reversal_view`.`SubAccountID` AS `SubAccountID`, `ledger_account`.`AccountName` AS `SubAccountName`, `temp_transaction_reversal_view`.`Mode` AS `Mode`, `temp_transaction_reversal_view`.`ReceiptNo` AS `ReceiptNo`, `temp_transaction_reversal_view`.`Dr` AS `Dr`, `temp_transaction_reversal_view`.`Cr` AS `Cr`, `temp_transaction_reversal_view`.`Date` AS `Date`, `temp_transaction_reversal_view`.`Time` AS `Time`, `temp_transaction_reversal_view`.`Username` AS `Username` FROM (`temp_transaction_reversal_view` join `ledger_account` on(`temp_transaction_reversal_view`.`SubAccountID` = `ledger_account`.`AccountNo`)) ;

-- --------------------------------------------------------

--
-- Structure for view `test_setup_view`
--
DROP TABLE IF EXISTS `test_setup_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `test_setup_view`  AS SELECT `test_setup`.`CouponNo` AS `CouponNo`, `academic_coupon`.`Title` AS `Title`, `test_setup`.`Type` AS `Type`, `test_setup`.`TestID` AS `TestID`, `test_setup`.`TestName` AS `TestName`, `test_setup`.`MaxScore` AS `MaxScore`, `test_setup`.`Username` AS `Username`, `test_setup`.`Date` AS `Date`, `test_setup`.`Time` AS `Time`, `test_setup`.`Status` AS `Status` FROM (`test_setup` join `academic_coupon` on(`test_setup`.`CouponNo` = `academic_coupon`.`CouponNo`)) WHERE `academic_coupon`.`Status` = '1' ;

-- --------------------------------------------------------

--
-- Structure for view `ticket_terminal_rpt_unauth`
--
DROP TABLE IF EXISTS `ticket_terminal_rpt_unauth`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ticket_terminal_rpt_unauth`  AS SELECT `algor`.`TicketNo` AS `TicketNo`, `algor`.`SubClassID` AS `SubClassID`, `subclass_form_master`.`StaffID` AS `StaffID`, `algor`.`StudentID` AS `StudentID`, `student_main_view`.`FullName` AS `FullName`, `algor`.`CouponNo` AS `CouponNo`, `algor`.`Validation` AS `Validation`, `algor`.`Date` AS `Date`, `algor`.`Time` AS `Time`, `algor`.`Username` AS `Username`, `algor`.`Status` AS `Status` FROM ((`algor` join `subclass_form_master` on(`algor`.`SubClassID` = `subclass_form_master`.`SubClassID`)) join `student_main_view` on(`algor`.`StudentID` = `student_main_view`.`StudentID`)) WHERE `algor`.`Status` = 2 ;

-- --------------------------------------------------------

--
-- Structure for view `tracked_shipment`
--
DROP TABLE IF EXISTS `tracked_shipment`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `tracked_shipment`  AS SELECT `container_main`.`ConsignmentID` AS `ConsignmentID`, `container_main`.`CarrierID` AS `CarrierID`, `container_main`.`Rotation` AS `Rotation`, `container_main`.`ShipperID` AS `ShipperID`, `container_main`.`VesselName` AS `VesselName`, `container_main`.`VoyageNo` AS `VoyageNo`, `container_main`.`SealNo` AS `SealNo`, `container_main`.`ETA` AS `ETA`, CASE WHEN `container_main`.`Status` = 1 THEN 'On The Way' WHEN `container_main`.`Status` = 2 THEN 'Arrived' WHEN `container_main`.`Status` = 3 THEN 'Arrived and Stuffed' END AS `ArrivalStatus`, `container_main`.`BL` AS `BL`, `container_main`.`ContainerNo` AS `ContainerNo`, `container_main`.`ContainerSize` AS `ContainerSize`, `container_main`.`ContWeight` AS `ContWeight`, `container_main`.`Charges` AS `Charges`, `container_main`.`AgentContact` AS `AgentContact`, `container_main`.`Status` AS `Status` FROM `container_main` WHERE `container_main`.`Status` <> 0 ;

-- --------------------------------------------------------

--
-- Structure for view `tracked_shipment_0`
--
DROP TABLE IF EXISTS `tracked_shipment_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `tracked_shipment_0`  AS SELECT `tracked_shipment`.`ConsignmentID` AS `ConsignmentID`, `tracked_shipment`.`CarrierID` AS `CarrierID`, `tracked_shipment`.`Rotation` AS `Rotation`, `tracked_shipment`.`ShipperID` AS `ShipperID`, `tracked_shipment`.`VesselName` AS `VesselName`, `tracked_shipment`.`VoyageNo` AS `VoyageNo`, `tracked_shipment`.`SealNo` AS `SealNo`, `tracked_shipment`.`ETA` AS `ETA`, to_days(`tracked_shipment`.`ETA`) - to_days(curdate()) AS `ETA_Days`, `tracked_shipment`.`ArrivalStatus` AS `ArrivalStatus`, `tracked_shipment`.`BL` AS `BL`, `tracked_shipment`.`ContainerNo` AS `ContainerNo`, `tracked_shipment`.`ContainerSize` AS `ContainerSize`, `tracked_shipment`.`ContWeight` AS `ContWeight`, `tracked_shipment`.`Charges` AS `Charges`, `tracked_shipment`.`AgentContact` AS `AgentContact`, `tracked_shipment`.`Status` AS `Status` FROM `tracked_shipment` WHERE 1 ;

-- --------------------------------------------------------

--
-- Structure for view `unassign_student_sub_class`
--
DROP TABLE IF EXISTS `unassign_student_sub_class`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `unassign_student_sub_class`  AS SELECT `student_main`.`Year` AS `Year`, `student_main`.`Code` AS `Code`, `student_main`.`StudentID` AS `StudentID`, concat(`student_main`.`FirstName`,' ',`student_main`.`LastName`) AS `FullName`, `student_main`.`Gender` AS `Gender`, `student_main`.`DOB` AS `DOB`, `student_main`.`CourseID` AS `CourseID`, `class_main`.`ClassName` AS `CourseName`, `student_main`.`ClassAdmiitted` AS `ClassAdmiitted`, `student_main`.`AdmissionDate` AS `AdmissionDate`, `student_main`.`BoarderStats` AS `BoarderStats`, `student_main`.`LastSchool` AS `LastSchool`, `student_main`.`AgregateResult` AS `AgregateResult`, `student_main`.`HouseID` AS `HouseID`, `student_main`.`FatherName` AS `FatherName`, `student_main`.`MotherName` AS `MotherName`, `student_main`.`TelNo` AS `TelNo`, `student_main`.`Occupation` AS `Occupation`, `student_main`.`Address` AS `Address`, `student_main`.`Date` AS `Date`, `student_main`.`Time` AS `Time`, `student_main`.`Username` AS `Username`, `student_main`.`Status` AS `Status` FROM (`student_main` join `class_main` on(`student_main`.`CourseID` = `class_main`.`ClassID`)) WHERE `student_main`.`ClassAdmiitted` = '' ;

-- --------------------------------------------------------

--
-- Structure for view `unauthorise_fee_charges`
--
DROP TABLE IF EXISTS `unauthorise_fee_charges`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `unauthorise_fee_charges`  AS SELECT `student_fee`.`StudentID` AS `StudentID`, `student_fee`.`SubClassID` AS `SubClassID`, `student_fee`.`AccountNo` AS `AccountNo`, `student_fee`.`Stamp` AS `Stamp`, `student_fee`.`CouponID` AS `CouponID`, `student_fee`.`Description` AS `Description`, `student_fee`.`ReceiptNo` AS `ReceiptNo`, `student_fee`.`Dr` AS `Dr`, `student_fee`.`Cr` AS `Cr`, `student_fee`.`Date` AS `Date`, `student_fee`.`Time` AS `Time`, `student_fee`.`Username` AS `Username`, `student_fee`.`Status` AS `Status` FROM `student_fee` WHERE `student_fee`.`Status` <> '1' ;

-- --------------------------------------------------------

--
-- Structure for view `unauthorise_fee_charges_0`
--
DROP TABLE IF EXISTS `unauthorise_fee_charges_0`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `unauthorise_fee_charges_0`  AS SELECT `unauthorise_fee_charges`.`StudentID` AS `StudentID`, `unauthorise_fee_charges`.`SubClassID` AS `SubClassID`, `unauthorise_fee_charges`.`Stamp` AS `Stamp`, `unauthorise_fee_charges`.`CouponID` AS `CouponID`, `unauthorise_fee_charges`.`Description` AS `Description`, `unauthorise_fee_charges`.`ReceiptNo` AS `ReceiptNo`, round(sum(`unauthorise_fee_charges`.`Dr`),2) AS `TDr`, round(sum(`unauthorise_fee_charges`.`Cr`),2) AS `TCr`, `unauthorise_fee_charges`.`Date` AS `Date`, `unauthorise_fee_charges`.`Username` AS `Username`, `unauthorise_fee_charges`.`Status` AS `Status` FROM `unauthorise_fee_charges` GROUP BY `unauthorise_fee_charges`.`StudentID`, `unauthorise_fee_charges`.`ReceiptNo`, `unauthorise_fee_charges`.`SubClassID` ;

-- --------------------------------------------------------

--
-- Structure for view `unauthorise_fee_charges_1`
--
DROP TABLE IF EXISTS `unauthorise_fee_charges_1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `unauthorise_fee_charges_1`  AS SELECT `unauthorise_fee_charges_0`.`StudentID` AS `StudentID`, `student_main_view`.`FullName` AS `FullName`, `unauthorise_fee_charges_0`.`SubClassID` AS `SubClassID`, `sub_class_main_view`.`SubClassName` AS `SubClassName`, `unauthorise_fee_charges_0`.`Stamp` AS `Stamp`, `unauthorise_fee_charges_0`.`CouponID` AS `CouponID`, `unauthorise_fee_charges_0`.`Description` AS `Description`, `unauthorise_fee_charges_0`.`ReceiptNo` AS `ReceiptNo`, `unauthorise_fee_charges_0`.`TDr` AS `TDr`, `unauthorise_fee_charges_0`.`TCr` AS `TCr`, `unauthorise_fee_charges_0`.`Date` AS `Date`, `unauthorise_fee_charges_0`.`Username` AS `Username`, `unauthorise_fee_charges_0`.`Status` AS `Status` FROM ((`unauthorise_fee_charges_0` join `student_main_view` on(`unauthorise_fee_charges_0`.`StudentID` = `student_main_view`.`StudentID`)) join `sub_class_main_view` on(`unauthorise_fee_charges_0`.`SubClassID` = `sub_class_main_view`.`SubClassID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `unauthorise_gl_transaction`
--
DROP TABLE IF EXISTS `unauthorise_gl_transaction`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `unauthorise_gl_transaction`  AS SELECT `journal_view`.`ControlID` AS `ControlID`, `journal_view`.`CategoryID` AS `CategoryID`, `journal_view`.`AccountID` AS `AccountID`, `journal_view`.`AccountName` AS `AccountName`, `journal_view`.`SubAccountID` AS `SubAccountID`, `journal_view`.`Mode` AS `Mode`, `journal_view`.`TType` AS `TType`, `journal_view`.`ReceiptNo` AS `ReceiptNo`, `journal_view`.`Dr` AS `Dr`, `journal_view`.`Cr` AS `Cr`, `journal_view`.`Description` AS `Description`, `journal_view`.`Date` AS `Date`, `journal_view`.`Time` AS `Time`, `journal_view`.`Username` AS `Username`, `journal_view`.`Authorizer` AS `Authorizer`, `journal_view`.`BranchID` AS `BranchID`, `journal_view`.`Status` AS `Status` FROM `journal_view` WHERE `journal_view`.`Status` <> 1 ;

-- --------------------------------------------------------

--
-- Structure for view `unauthorise_pnl_transaction`
--
DROP TABLE IF EXISTS `unauthorise_pnl_transaction`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `unauthorise_pnl_transaction`  AS SELECT `journal_view`.`ControlID` AS `ControlID`, `journal_view`.`CategoryID` AS `CategoryID`, `journal_view`.`AccountID` AS `AccountID`, `journal_view`.`AccountName` AS `AccountName`, `journal_view`.`SubAccountID` AS `SubAccountID`, `ledger_account`.`AccountName` AS `SubAccountName`, `journal_view`.`Mode` AS `Mode`, `journal_view`.`TType` AS `TType`, `journal_view`.`ReceiptNo` AS `ReceiptNo`, `journal_view`.`Dr` AS `Dr`, `journal_view`.`Cr` AS `Cr`, `journal_view`.`Description` AS `Description`, `ledger_account`.`Type` AS `Type`, `journal_view`.`Date` AS `Date`, `journal_view`.`Time` AS `Time`, `journal_view`.`Username` AS `Username`, `journal_view`.`Authorizer` AS `Authorizer`, `journal_view`.`BranchID` AS `BranchID`, `journal_view`.`Status` AS `Status` FROM (`journal_view` join `ledger_account` on(`journal_view`.`SubAccountID` = `ledger_account`.`AccountNo`)) WHERE `journal_view`.`Status` = '4' ;

-- --------------------------------------------------------

--
-- Structure for view `users_view`
--
DROP TABLE IF EXISTS `users_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `users_view`  AS SELECT `users`.`MemberID` AS `MemberID`, `student_list`.`FullName` AS `FullName`, `users`.`Picture` AS `Picture`, `users`.`Created` AS `Created`, `users`.`Modified` AS `Modified` FROM (`users` join `student_list` on(`users`.`MemberID` = `student_list`.`StudentID`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_coupon`
--
ALTER TABLE `academic_coupon`
  ADD PRIMARY KEY (`CouponNo`),
  ADD UNIQUE KEY `Date` (`Date`);

--
-- Indexes for table `active_account_receivable`
--
ALTER TABLE `active_account_receivable`
  ADD PRIMARY KEY (`AccountNo`);

--
-- Indexes for table `active_bank_cash`
--
ALTER TABLE `active_bank_cash`
  ADD PRIMARY KEY (`AccountID`);

--
-- Indexes for table `active_consignment_revenue`
--
ALTER TABLE `active_consignment_revenue`
  ADD PRIMARY KEY (`AccountNo`);

--
-- Indexes for table `active_declaration_income`
--
ALTER TABLE `active_declaration_income`
  ADD PRIMARY KEY (`AccountNo`);

--
-- Indexes for table `active_handling_cost`
--
ALTER TABLE `active_handling_cost`
  ADD PRIMARY KEY (`AccountNo`);

--
-- Indexes for table `active_ie`
--
ALTER TABLE `active_ie`
  ADD PRIMARY KEY (`AccountID`);

--
-- Indexes for table `active_momo`
--
ALTER TABLE `active_momo`
  ADD PRIMARY KEY (`AccountNo`);

--
-- Indexes for table `active_petty_cash`
--
ALTER TABLE `active_petty_cash`
  ADD PRIMARY KEY (`AccountNo`,`Username`),
  ADD KEY `Username` (`Username`);

--
-- Indexes for table `active_service_charge`
--
ALTER TABLE `active_service_charge`
  ADD PRIMARY KEY (`AccountNo`);

--
-- Indexes for table `active_vault`
--
ALTER TABLE `active_vault`
  ADD PRIMARY KEY (`AccountNo`);

--
-- Indexes for table `active_write_off`
--
ALTER TABLE `active_write_off`
  ADD PRIMARY KEY (`AccountNo`);

--
-- Indexes for table `algor`
--
ALTER TABLE `algor`
  ADD PRIMARY KEY (`TicketNo`);

--
-- Indexes for table `all_date`
--
ALTER TABLE `all_date`
  ADD PRIMARY KEY (`TDate`);

--
-- Indexes for table `bank_details`
--
ALTER TABLE `bank_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `billing_board`
--
ALTER TABLE `billing_board`
  ADD PRIMARY KEY (`ReceiptNo`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `ccdb`
--
ALTER TABLE `ccdb`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `charge_taxes`
--
ALTER TABLE `charge_taxes`
  ADD PRIMARY KEY (`GetFund`);

--
-- Indexes for table `class_category`
--
ALTER TABLE `class_category`
  ADD PRIMARY KEY (`CategoryID`);

--
-- Indexes for table `class_main`
--
ALTER TABLE `class_main`
  ADD PRIMARY KEY (`ClassID`),
  ADD KEY `CategoryID` (`CategoryID`);

--
-- Indexes for table `class_subject`
--
ALTER TABLE `class_subject`
  ADD PRIMARY KEY (`ClassID`,`SubjCategoryID`,`SubjectID`),
  ADD KEY `SubjectID` (`SubjectID`),
  ADD KEY `SubjCategoryID` (`SubjCategoryID`);

--
-- Indexes for table `commodity_category`
--
ALTER TABLE `commodity_category`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `commodity_type`
--
ALTER TABLE `commodity_type`
  ADD PRIMARY KEY (`TypeID`),
  ADD KEY `CategoryID` (`CategoryID`);

--
-- Indexes for table `consignee_main`
--
ALTER TABLE `consignee_main`
  ADD PRIMARY KEY (`ConsigneeID`);

--
-- Indexes for table `consignment_weight_temp`
--
ALTER TABLE `consignment_weight_temp`
  ADD PRIMARY KEY (`MainBL`,`HBL`,`ConsignmentID`,`Username`);

--
-- Indexes for table `container_cmdts_temp`
--
ALTER TABLE `container_cmdts_temp`
  ADD PRIMARY KEY (`ContainerSize`);

--
-- Indexes for table `container_details`
--
ALTER TABLE `container_details`
  ADD PRIMARY KEY (`ConsignmentID`,`BL`,`SealNo`,`ContainerNo`,`ContainerSize`,`Weight`,`HandlingCost`,`Username`,`BranchID`,`Date`,`Time`);

--
-- Indexes for table `container_main`
--
ALTER TABLE `container_main`
  ADD PRIMARY KEY (`ConsignmentID`,`BL`),
  ADD UNIQUE KEY `BL` (`BL`),
  ADD KEY `CmdtTypeID` (`CmdtTypeID`),
  ADD KEY `ReleaseType` (`ReleaseType`);

--
-- Indexes for table `container_release`
--
ALTER TABLE `container_release`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `ctrl_fee_receivable`
--
ALTER TABLE `ctrl_fee_receivable`
  ADD PRIMARY KEY (`AccountID`);

--
-- Indexes for table `ctrl_student`
--
ALTER TABLE `ctrl_student`
  ADD PRIMARY KEY (`AccountID`);

--
-- Indexes for table `currency_conversion`
--
ALTER TABLE `currency_conversion`
  ADD PRIMARY KEY (`Rate`,`Currency`,`Username`);

--
-- Indexes for table `declaration_main`
--
ALTER TABLE `declaration_main`
  ADD PRIMARY KEY (`DeclarationID`),
  ADD KEY `BL` (`BL`),
  ADD KEY `ReceiptNo` (`ReceiptNo`),
  ADD KEY `idx_main_bl` (`BL`);

--
-- Indexes for table `disburement_income_account`
--
ALTER TABLE `disburement_income_account`
  ADD PRIMARY KEY (`AccountNo`);

--
-- Indexes for table `disburement_user_auth`
--
ALTER TABLE `disburement_user_auth`
  ADD PRIMARY KEY (`Authorisor`);

--
-- Indexes for table `disbursement_accounts`
--
ALTER TABLE `disbursement_accounts`
  ADD PRIMARY KEY (`AccountNo`);

--
-- Indexes for table `disbursement_analysis`
--
ALTER TABLE `disbursement_analysis`
  ADD PRIMARY KEY (`ConsigneeID`,`BL`,`HBL`,`ContainerNo`,`ReceiptNo`,`AccountID`),
  ADD KEY `ConsigneeID` (`ConsigneeID`,`BL`,`HBL`,`ContainerNo`,`AccountID`),
  ADD KEY `ReceiptNo` (`ReceiptNo`);

--
-- Indexes for table `disbursement_temp_analysis`
--
ALTER TABLE `disbursement_temp_analysis`
  ADD PRIMARY KEY (`BL`,`HouseBL`,`ConsigneeID`,`AccountNo`) USING BTREE;

--
-- Indexes for table `disbursment_gateout_truck_details`
--
ALTER TABLE `disbursment_gateout_truck_details`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BL` (`BL`),
  ADD KEY `ReceiptNo` (`ReceiptNo`);

--
-- Indexes for table `eta_web_track`
--
ALTER TABLE `eta_web_track`
  ADD PRIMARY KEY (`ConsignmentID`,`MainBL`,`Time`),
  ADD KEY `MainBL` (`MainBL`);

--
-- Indexes for table `e_delivery_order_request`
--
ALTER TABLE `e_delivery_order_request`
  ADD PRIMARY KEY (`HouseBL`),
  ADD KEY `HouseBL` (`HouseBL`);

--
-- Indexes for table `e_payment_confirmation`
--
ALTER TABLE `e_payment_confirmation`
  ADD PRIMARY KEY (`HouseBL`),
  ADD KEY `HouseBL` (`HouseBL`),
  ADD KEY `HouseBL_2` (`HouseBL`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fee_account_order`
--
ALTER TABLE `fee_account_order`
  ADD PRIMARY KEY (`AccountID`),
  ADD UNIQUE KEY `Order` (`Order`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`GroupID`);

--
-- Indexes for table `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`GroupID`,`MemberID`);

--
-- Indexes for table `handling_charge`
--
ALTER TABLE `handling_charge`
  ADD PRIMARY KEY (`AccountNo`);

--
-- Indexes for table `hbl_invoice`
--
ALTER TABLE `hbl_invoice`
  ADD PRIMARY KEY (`ConsignmentID`,`MainBL`,`HouseBL`,`ConsigneeID`,`AccountNo`),
  ADD KEY `ReceiptNo` (`ReceiptNo`),
  ADD KEY `HouseBL` (`HouseBL`);

--
-- Indexes for table `hbl_invoice_consignee_temp`
--
ALTER TABLE `hbl_invoice_consignee_temp`
  ADD PRIMARY KEY (`ConsignmentID`,`MainBL`,`HouseBL`,`ConsigneeID`,`AccountNo`,`Username`);

--
-- Indexes for table `house`
--
ALTER TABLE `house`
  ADD PRIMARY KEY (`HouseID`);

--
-- Indexes for table `inst_branch`
--
ALTER TABLE `inst_branch`
  ADD PRIMARY KEY (`BranchID`),
  ADD KEY `InsID` (`InstID`);

--
-- Indexes for table `inst_reg`
--
ALTER TABLE `inst_reg`
  ADD PRIMARY KEY (`InstID`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `journal`
--
ALTER TABLE `journal`
  ADD KEY `ReceiptNo` (`ReceiptNo`),
  ADD KEY `AccountID` (`AccountID`);

--
-- Indexes for table `kaina`
--
ALTER TABLE `kaina`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `ledger_account`
--
ALTER TABLE `ledger_account`
  ADD PRIMARY KEY (`AccountNo`),
  ADD KEY `ControlID` (`ControlID`),
  ADD KEY `CategoryID` (`CategoryID`);

--
-- Indexes for table `ledger_category`
--
ALTER TABLE `ledger_category`
  ADD PRIMARY KEY (`SubCategoryID`);

--
-- Indexes for table `ledger_control`
--
ALTER TABLE `ledger_control`
  ADD PRIMARY KEY (`ControlID`);

--
-- Indexes for table `manifestation_breakdown`
--
ALTER TABLE `manifestation_breakdown`
  ADD PRIMARY KEY (`ConsignmentID`,`MainBL`,`HouseBL`,`ContainerNo`),
  ADD KEY `ConsigneeID` (`ConsigneeID`),
  ADD KEY `MainBL` (`MainBL`,`ContainerNo`),
  ADD KEY `idx_main_bl` (`MainBL`),
  ADD KEY `idx_house_bl` (`HouseBL`);

--
-- Indexes for table `map_admission_fee`
--
ALTER TABLE `map_admission_fee`
  ADD PRIMARY KEY (`ClassID`,`AccountID`),
  ADD KEY `AccountID` (`AccountID`);

--
-- Indexes for table `map_school_fee`
--
ALTER TABLE `map_school_fee`
  ADD PRIMARY KEY (`SubClassID`,`AccountID`),
  ADD KEY `AccountID` (`AccountID`);

--
-- Indexes for table `member_temp_selection`
--
ALTER TABLE `member_temp_selection`
  ADD PRIMARY KEY (`MemberID`,`Username`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `my_post`
--
ALTER TABLE `my_post`
  ADD PRIMARY KEY (`postid`);

--
-- Indexes for table `my_post_reply`
--
ALTER TABLE `my_post_reply`
  ADD PRIMARY KEY (`postid`,`MemberID`,`Date`,`Time`),
  ADD KEY `postid` (`postid`),
  ADD KEY `MemberID` (`MemberID`);

--
-- Indexes for table `my_post_viewers`
--
ALTER TABLE `my_post_viewers`
  ADD PRIMARY KEY (`postid`,`MemberID`);

--
-- Indexes for table `new_comtainer_cmdts_temp`
--
ALTER TABLE `new_comtainer_cmdts_temp`
  ADD PRIMARY KEY (`ContainerNo`);

--
-- Indexes for table `new_container_temp`
--
ALTER TABLE `new_container_temp`
  ADD PRIMARY KEY (`BOL`,`SealNo`,`ContainerNo`,`ContainerSize`,`Weight`,`HandlingCost`,`Username`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`MakerID`,`Time`);

--
-- Indexes for table `other_invoice`
--
ALTER TABLE `other_invoice`
  ADD PRIMARY KEY (`ClientID`,`AccountNo`,`Stamp`,`ReceiptNo`,`Amount`,`GetFundNHIL`,`VAT`,`Date`,`Time`,`Username`,`Status`),
  ADD KEY `ReceiptNo` (`ReceiptNo`);

--
-- Indexes for table `package_unit`
--
ALTER TABLE `package_unit`
  ADD PRIMARY KEY (`Unit`);

--
-- Indexes for table `pnl_transaction`
--
ALTER TABLE `pnl_transaction`
  ADD PRIMARY KEY (`AccountID`,`ReceiptNo`,`Dr`,`Cr`,`Date`,`Time`,`MainBL`,`HouseBL`) USING BTREE,
  ADD KEY `ReceiptNo` (`ReceiptNo`);

--
-- Indexes for table `pod`
--
ALTER TABLE `pod`
  ADD PRIMARY KEY (`POD_ID`);

--
-- Indexes for table `pol`
--
ALTER TABLE `pol`
  ADD PRIMARY KEY (`POL_ID`);

--
-- Indexes for table `post_ids`
--
ALTER TABLE `post_ids`
  ADD PRIMARY KEY (`PID`);

--
-- Indexes for table `receipt_main`
--
ALTER TABLE `receipt_main`
  ADD PRIMARY KEY (`ReceiptNo`);

--
-- Indexes for table `receipt_no`
--
ALTER TABLE `receipt_no`
  ADD PRIMARY KEY (`ReceiptNo`,`Username`);

--
-- Indexes for table `rpt_multi_values`
--
ALTER TABLE `rpt_multi_values`
  ADD PRIMARY KEY (`Username`);

--
-- Indexes for table `rpt_multi_values_0`
--
ALTER TABLE `rpt_multi_values_0`
  ADD PRIMARY KEY (`Username`);

--
-- Indexes for table `service_charge_main`
--
ALTER TABLE `service_charge_main`
  ADD PRIMARY KEY (`ServiceID`),
  ADD KEY `ReceiptNo` (`ReceiptNo`);

--
-- Indexes for table `set_accounts`
--
ALTER TABLE `set_accounts`
  ADD PRIMARY KEY (`PNL`);

--
-- Indexes for table `shipper_main`
--
ALTER TABLE `shipper_main`
  ADD PRIMARY KEY (`ShipperID`);

--
-- Indexes for table `ship_carrier`
--
ALTER TABLE `ship_carrier`
  ADD PRIMARY KEY (`CarrierID`);

--
-- Indexes for table `staff_class_subj_mapp`
--
ALTER TABLE `staff_class_subj_mapp`
  ADD PRIMARY KEY (`StaffID`,`SubClassID`,`SubjectID`),
  ADD KEY `SubjectID` (`SubjectID`),
  ADD KEY `staff_class_subj_mapp_ibfk_2` (`SubClassID`);

--
-- Indexes for table `staff_main`
--
ALTER TABLE `staff_main`
  ADD PRIMARY KEY (`StaffID`);

--
-- Indexes for table `student_fee`
--
ALTER TABLE `student_fee`
  ADD PRIMARY KEY (`StudentID`,`SubClassID`,`AccountNo`,`Stamp`,`ReceiptNo`,`Dr`,`Cr`,`Date`,`Time`,`Username`,`Status`),
  ADD KEY `ReceiptNo` (`ReceiptNo`);

--
-- Indexes for table `student_id`
--
ALTER TABLE `student_id`
  ADD PRIMARY KEY (`Username`);

--
-- Indexes for table `student_import`
--
ALTER TABLE `student_import`
  ADD PRIMARY KEY (`STDID`);

--
-- Indexes for table `student_list`
--
ALTER TABLE `student_list`
  ADD PRIMARY KEY (`StudentID`);

--
-- Indexes for table `student_main`
--
ALTER TABLE `student_main`
  ADD PRIMARY KEY (`StudentID`),
  ADD KEY `ClassAdmiitted` (`ClassAdmiitted`),
  ADD KEY `AgregateResult` (`AgregateResult`),
  ADD KEY `HouseID` (`HouseID`),
  ADD KEY `CourseID` (`CourseID`);

--
-- Indexes for table `student_promo`
--
ALTER TABLE `student_promo`
  ADD PRIMARY KEY (`StudentID`,`PreviousClass`,`PromoClass`,`Date`,`Time`,`Username`,`Status`),
  ADD KEY `StudentID` (`StudentID`),
  ADD KEY `PromoClass` (`PromoClass`);

--
-- Indexes for table `student_register`
--
ALTER TABLE `student_register`
  ADD PRIMARY KEY (`StudentID`,`SubClassID`,`Date`);

--
-- Indexes for table `student_register_subject`
--
ALTER TABLE `student_register_subject`
  ADD PRIMARY KEY (`StudentID`,`SubClassID`,`SubjectID`,`Date`);

--
-- Indexes for table `student_test_remarks`
--
ALTER TABLE `student_test_remarks`
  ADD PRIMARY KEY (`StudentID`,`CouponNo`,`SubClassID`);

--
-- Indexes for table `student_test_score`
--
ALTER TABLE `student_test_score`
  ADD PRIMARY KEY (`CouponNo`,`StudentID`,`SubClassID`,`TestType`,`TestID`,`SubjectID`,`Username`);

--
-- Indexes for table `subclass_form_master`
--
ALTER TABLE `subclass_form_master`
  ADD PRIMARY KEY (`SubClassID`),
  ADD KEY `StaffID` (`StaffID`);

--
-- Indexes for table `subclass_master`
--
ALTER TABLE `subclass_master`
  ADD PRIMARY KEY (`StaffID`),
  ADD KEY `SubClassID` (`SubClassID`);

--
-- Indexes for table `subject_main`
--
ALTER TABLE `subject_main`
  ADD PRIMARY KEY (`SubjectID`),
  ADD KEY `CategoryID` (`CategoryID`);

--
-- Indexes for table `subj_category`
--
ALTER TABLE `subj_category`
  ADD PRIMARY KEY (`CategoryID`);

--
-- Indexes for table `sub_class_id`
--
ALTER TABLE `sub_class_id`
  ADD PRIMARY KEY (`Username`);

--
-- Indexes for table `sub_class_main`
--
ALTER TABLE `sub_class_main`
  ADD PRIMARY KEY (`SubClassID`),
  ADD KEY `CourseID` (`CourseID`);

--
-- Indexes for table `sub_class_subject`
--
ALTER TABLE `sub_class_subject`
  ADD PRIMARY KEY (`CourseID`,`SubClassID`,`SubjectID`);

--
-- Indexes for table `tax_components`
--
ALTER TABLE `tax_components`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `temp_class_subj_map`
--
ALTER TABLE `temp_class_subj_map`
  ADD PRIMARY KEY (`SubjectID`);

--
-- Indexes for table `temp_general_student_billing`
--
ALTER TABLE `temp_general_student_billing`
  ADD PRIMARY KEY (`StudentID`,`SubClassID`,`Username`),
  ADD KEY `SubClassID` (`SubClassID`);

--
-- Indexes for table `temp_ind_std_bill_account`
--
ALTER TABLE `temp_ind_std_bill_account`
  ADD PRIMARY KEY (`StudentID`,`SubClassID`,`AccountID`);

--
-- Indexes for table `temp_manifestation_breakdown`
--
ALTER TABLE `temp_manifestation_breakdown`
  ADD PRIMARY KEY (`HouseBL`);

--
-- Indexes for table `temp_other_invoice`
--
ALTER TABLE `temp_other_invoice`
  ADD PRIMARY KEY (`AccountNo`,`Username`);

--
-- Indexes for table `temp_other_invoice_non_manifest`
--
ALTER TABLE `temp_other_invoice_non_manifest`
  ADD PRIMARY KEY (`AccountNo`,`Username`);

--
-- Indexes for table `temp_staff_class_subj_mapp`
--
ALTER TABLE `temp_staff_class_subj_mapp`
  ADD PRIMARY KEY (`StaffID`,`ClassID`,`SubjectID`),
  ADD KEY `SubjectID` (`SubjectID`),
  ADD KEY `temp_staff_class_subj_mapp_ibfk_2` (`ClassID`);

--
-- Indexes for table `temp_student_admission_fee`
--
ALTER TABLE `temp_student_admission_fee`
  ADD PRIMARY KEY (`StudentID`);

--
-- Indexes for table `temp_student_register`
--
ALTER TABLE `temp_student_register`
  ADD PRIMARY KEY (`StudentID`,`Username`);

--
-- Indexes for table `temp_student_test_score`
--
ALTER TABLE `temp_student_test_score`
  ADD PRIMARY KEY (`StudentID`,`SubClassID`,`SubjectID`,`TestID`,`Username`);

--
-- Indexes for table `temp_subject_register`
--
ALTER TABLE `temp_subject_register`
  ADD PRIMARY KEY (`StudentID`,`SubClassID`,`SubjectID`,`Username`);

--
-- Indexes for table `temp_sub_class_subj_map`
--
ALTER TABLE `temp_sub_class_subj_map`
  ADD PRIMARY KEY (`SubjectID`);

--
-- Indexes for table `testconduct`
--
ALTER TABLE `testconduct`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `testfmaster`
--
ALTER TABLE `testfmaster`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `testinterest`
--
ALTER TABLE `testinterest`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `test_score`
--
ALTER TABLE `test_score`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `test_setup`
--
ALTER TABLE `test_setup`
  ADD PRIMARY KEY (`TestID`);

--
-- Indexes for table `test_type`
--
ALTER TABLE `test_type`
  ADD PRIMARY KEY (`TypeID`);

--
-- Indexes for table `t_date`
--
ALTER TABLE `t_date`
  ADD PRIMARY KEY (`ActiveDate`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`MemberID`);

--
-- Indexes for table `user_auth`
--
ALTER TABLE `user_auth`
  ADD PRIMARY KEY (`Username`);

--
-- Indexes for table `user_expense_petty_cash`
--
ALTER TABLE `user_expense_petty_cash`
  ADD PRIMARY KEY (`Username`);

--
-- Indexes for table `user_login_logs`
--
ALTER TABLE `user_login_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_login_logs_archive`
--
ALTER TABLE `user_login_logs_archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `waybill_main`
--
ALTER TABLE `waybill_main`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bank_details`
--
ALTER TABLE `bank_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ccdb`
--
ALTER TABLE `ccdb`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `commodity_category`
--
ALTER TABLE `commodity_category`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `commodity_type`
--
ALTER TABLE `commodity_type`
  MODIFY `TypeID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consignee_main`
--
ALTER TABLE `consignee_main`
  MODIFY `ConsigneeID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `container_cmdts_temp`
--
ALTER TABLE `container_cmdts_temp`
  MODIFY `ContainerSize` float NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `container_release`
--
ALTER TABLE `container_release`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `disbursment_gateout_truck_details`
--
ALTER TABLE `disbursment_gateout_truck_details`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `house`
--
ALTER TABLE `house`
  MODIFY `HouseID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_account`
--
ALTER TABLE `ledger_account`
  MODIFY `AccountNo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_category`
--
ALTER TABLE `ledger_category`
  MODIFY `SubCategoryID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_control`
--
ALTER TABLE `ledger_control`
  MODIFY `ControlID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pod`
--
ALTER TABLE `pod`
  MODIFY `POD_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pol`
--
ALTER TABLE `pol`
  MODIFY `POL_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shipper_main`
--
ALTER TABLE `shipper_main`
  MODIFY `ShipperID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ship_carrier`
--
ALTER TABLE `ship_carrier`
  MODIFY `CarrierID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tax_components`
--
ALTER TABLE `tax_components`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_score`
--
ALTER TABLE `test_score`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_setup`
--
ALTER TABLE `test_setup`
  MODIFY `TestID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_login_logs`
--
ALTER TABLE `user_login_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_login_logs_archive`
--
ALTER TABLE `user_login_logs_archive`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `waybill_main`
--
ALTER TABLE `waybill_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `active_bank_cash`
--
ALTER TABLE `active_bank_cash`
  ADD CONSTRAINT `active_bank_cash_ibfk_1` FOREIGN KEY (`AccountID`) REFERENCES `ledger_account` (`AccountNo`) ON UPDATE CASCADE;

--
-- Constraints for table `active_consignment_revenue`
--
ALTER TABLE `active_consignment_revenue`
  ADD CONSTRAINT `active_consignment_revenue_ibfk_1` FOREIGN KEY (`AccountNo`) REFERENCES `ledger_account` (`AccountNo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `active_declaration_income`
--
ALTER TABLE `active_declaration_income`
  ADD CONSTRAINT `active_declaration_income_ibfk_1` FOREIGN KEY (`AccountNo`) REFERENCES `ledger_account` (`AccountNo`) ON UPDATE CASCADE;

--
-- Constraints for table `active_handling_cost`
--
ALTER TABLE `active_handling_cost`
  ADD CONSTRAINT `active_handling_cost_ibfk_1` FOREIGN KEY (`AccountNo`) REFERENCES `ledger_account` (`AccountNo`) ON UPDATE CASCADE;

--
-- Constraints for table `active_ie`
--
ALTER TABLE `active_ie`
  ADD CONSTRAINT `active_ie_ibfk_1` FOREIGN KEY (`AccountID`) REFERENCES `ledger_account` (`AccountNo`) ON UPDATE CASCADE;

--
-- Constraints for table `active_momo`
--
ALTER TABLE `active_momo`
  ADD CONSTRAINT `active_momo_ibfk_1` FOREIGN KEY (`AccountNo`) REFERENCES `ledger_account` (`AccountNo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `active_petty_cash`
--
ALTER TABLE `active_petty_cash`
  ADD CONSTRAINT `active_petty_cash_ibfk_1` FOREIGN KEY (`AccountNo`) REFERENCES `ledger_account` (`AccountNo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `active_petty_cash_ibfk_2` FOREIGN KEY (`Username`) REFERENCES `kaina` (`ID`) ON UPDATE CASCADE;

--
-- Constraints for table `active_service_charge`
--
ALTER TABLE `active_service_charge`
  ADD CONSTRAINT `active_service_charge_ibfk_1` FOREIGN KEY (`AccountNo`) REFERENCES `ledger_account` (`AccountNo`) ON UPDATE CASCADE;

--
-- Constraints for table `active_vault`
--
ALTER TABLE `active_vault`
  ADD CONSTRAINT `active_vault_ibfk_1` FOREIGN KEY (`AccountNo`) REFERENCES `ledger_account` (`AccountNo`) ON UPDATE CASCADE;

--
-- Constraints for table `billing_board`
--
ALTER TABLE `billing_board`
  ADD CONSTRAINT `billing_board_ibfk_1` FOREIGN KEY (`ReceiptNo`) REFERENCES `receipt_main` (`ReceiptNo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `class_main`
--
ALTER TABLE `class_main`
  ADD CONSTRAINT `class_main_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `class_category` (`CategoryID`) ON UPDATE CASCADE;

--
-- Constraints for table `class_subject`
--
ALTER TABLE `class_subject`
  ADD CONSTRAINT `class_subject_ibfk_1` FOREIGN KEY (`ClassID`) REFERENCES `class_main` (`ClassID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `class_subject_ibfk_2` FOREIGN KEY (`SubjectID`) REFERENCES `subject_main` (`SubjectID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `class_subject_ibfk_3` FOREIGN KEY (`SubjCategoryID`) REFERENCES `subj_category` (`CategoryID`) ON UPDATE CASCADE;

--
-- Constraints for table `commodity_type`
--
ALTER TABLE `commodity_type`
  ADD CONSTRAINT `commodity_type_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `commodity_category` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `container_details`
--
ALTER TABLE `container_details`
  ADD CONSTRAINT `container_details_ibfk_1` FOREIGN KEY (`ConsignmentID`) REFERENCES `container_main` (`ConsignmentID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `container_main`
--
ALTER TABLE `container_main`
  ADD CONSTRAINT `container_main_ibfk_1` FOREIGN KEY (`CmdtTypeID`) REFERENCES `commodity_type` (`TypeID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `container_main_ibfk_2` FOREIGN KEY (`ReleaseType`) REFERENCES `container_release` (`ID`) ON UPDATE CASCADE;

--
-- Constraints for table `ctrl_fee_receivable`
--
ALTER TABLE `ctrl_fee_receivable`
  ADD CONSTRAINT `ctrl_fee_receivable_ibfk_1` FOREIGN KEY (`AccountID`) REFERENCES `ledger_account` (`AccountNo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ctrl_student`
--
ALTER TABLE `ctrl_student`
  ADD CONSTRAINT `ctrl_student_ibfk_1` FOREIGN KEY (`AccountID`) REFERENCES `ledger_account` (`AccountNo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `declaration_main`
--
ALTER TABLE `declaration_main`
  ADD CONSTRAINT `declaration_main_ibfk_1` FOREIGN KEY (`ReceiptNo`) REFERENCES `receipt_main` (`ReceiptNo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `disburement_income_account`
--
ALTER TABLE `disburement_income_account`
  ADD CONSTRAINT `disburement_income_account_ibfk_1` FOREIGN KEY (`AccountNo`) REFERENCES `ledger_account` (`AccountNo`) ON UPDATE CASCADE;

--
-- Constraints for table `disburement_user_auth`
--
ALTER TABLE `disburement_user_auth`
  ADD CONSTRAINT `disburement_user_auth_ibfk_1` FOREIGN KEY (`Authorisor`) REFERENCES `kaina` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `disbursement_analysis`
--
ALTER TABLE `disbursement_analysis`
  ADD CONSTRAINT `disbursement_analysis_ibfk_1` FOREIGN KEY (`ReceiptNo`) REFERENCES `receipt_main` (`ReceiptNo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `disbursment_gateout_truck_details`
--
ALTER TABLE `disbursment_gateout_truck_details`
  ADD CONSTRAINT `disbursment_gateout_truck_details_ibfk_1` FOREIGN KEY (`BL`) REFERENCES `container_main` (`BL`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `disbursment_gateout_truck_details_ibfk_2` FOREIGN KEY (`ReceiptNo`) REFERENCES `receipt_main` (`ReceiptNo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fee_account_order`
--
ALTER TABLE `fee_account_order`
  ADD CONSTRAINT `fee_account_order_ibfk_1` FOREIGN KEY (`AccountID`) REFERENCES `ledger_account` (`AccountNo`) ON UPDATE CASCADE;

--
-- Constraints for table `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `group_members_ibfk_1` FOREIGN KEY (`GroupID`) REFERENCES `groups` (`GroupID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hbl_invoice`
--
ALTER TABLE `hbl_invoice`
  ADD CONSTRAINT `hbl_invoice_ibfk_1` FOREIGN KEY (`ReceiptNo`) REFERENCES `receipt_main` (`ReceiptNo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `hbl_invoice_ibfk_2` FOREIGN KEY (`HouseBL`) REFERENCES `manifestation_breakdown` (`HouseBL`) ON UPDATE CASCADE;

--
-- Constraints for table `inst_branch`
--
ALTER TABLE `inst_branch`
  ADD CONSTRAINT `inst_branch_ibfk_1` FOREIGN KEY (`InstID`) REFERENCES `inst_reg` (`InstID`) ON UPDATE CASCADE;

--
-- Constraints for table `journal`
--
ALTER TABLE `journal`
  ADD CONSTRAINT `journal_ibfk_1` FOREIGN KEY (`ReceiptNo`) REFERENCES `receipt_main` (`ReceiptNo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `journal_ibfk_2` FOREIGN KEY (`AccountID`) REFERENCES `ledger_account` (`AccountNo`) ON UPDATE CASCADE;

--
-- Constraints for table `ledger_account`
--
ALTER TABLE `ledger_account`
  ADD CONSTRAINT `ledger_account_ibfk_1` FOREIGN KEY (`ControlID`) REFERENCES `ledger_control` (`ControlID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_account_ibfk_2` FOREIGN KEY (`CategoryID`) REFERENCES `ledger_category` (`SubCategoryID`) ON UPDATE CASCADE;

--
-- Constraints for table `manifestation_breakdown`
--
ALTER TABLE `manifestation_breakdown`
  ADD CONSTRAINT `manifestation_breakdown_ibfk_1` FOREIGN KEY (`ConsignmentID`) REFERENCES `container_main` (`ConsignmentID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `manifestation_breakdown_ibfk_2` FOREIGN KEY (`ConsigneeID`) REFERENCES `consignee_main` (`ConsigneeID`) ON UPDATE CASCADE;

--
-- Constraints for table `map_admission_fee`
--
ALTER TABLE `map_admission_fee`
  ADD CONSTRAINT `map_admission_fee_ibfk_2` FOREIGN KEY (`AccountID`) REFERENCES `ledger_account` (`AccountNo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `map_admission_fee_ibfk_3` FOREIGN KEY (`ClassID`) REFERENCES `class_main` (`ClassID`) ON UPDATE CASCADE;

--
-- Constraints for table `map_school_fee`
--
ALTER TABLE `map_school_fee`
  ADD CONSTRAINT `map_school_fee_ibfk_1` FOREIGN KEY (`SubClassID`) REFERENCES `sub_class_main` (`SubClassID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `map_school_fee_ibfk_2` FOREIGN KEY (`AccountID`) REFERENCES `ledger_account` (`AccountNo`) ON UPDATE CASCADE;

--
-- Constraints for table `other_invoice`
--
ALTER TABLE `other_invoice`
  ADD CONSTRAINT `other_invoice_ibfk_1` FOREIGN KEY (`ReceiptNo`) REFERENCES `receipt_main` (`ReceiptNo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pnl_transaction`
--
ALTER TABLE `pnl_transaction`
  ADD CONSTRAINT `pnl_transaction_ibfk_1` FOREIGN KEY (`ReceiptNo`) REFERENCES `receipt_main` (`ReceiptNo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `service_charge_main`
--
ALTER TABLE `service_charge_main`
  ADD CONSTRAINT `service_charge_main_ibfk_1` FOREIGN KEY (`ReceiptNo`) REFERENCES `receipt_main` (`ReceiptNo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `staff_class_subj_mapp`
--
ALTER TABLE `staff_class_subj_mapp`
  ADD CONSTRAINT `staff_class_subj_mapp_ibfk_1` FOREIGN KEY (`StaffID`) REFERENCES `staff_main` (`StaffID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `staff_class_subj_mapp_ibfk_2` FOREIGN KEY (`SubClassID`) REFERENCES `sub_class_main` (`SubClassID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `staff_class_subj_mapp_ibfk_3` FOREIGN KEY (`SubjectID`) REFERENCES `subject_main` (`SubjectID`) ON UPDATE CASCADE;

--
-- Constraints for table `student_fee`
--
ALTER TABLE `student_fee`
  ADD CONSTRAINT `student_fee_ibfk_1` FOREIGN KEY (`ReceiptNo`) REFERENCES `receipt_main` (`ReceiptNo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_fee_ibfk_2` FOREIGN KEY (`StudentID`) REFERENCES `consignee_main` (`ConsigneeID`) ON UPDATE CASCADE;

--
-- Constraints for table `student_main`
--
ALTER TABLE `student_main`
  ADD CONSTRAINT `student_main_ibfk_2` FOREIGN KEY (`HouseID`) REFERENCES `house` (`HouseID`) ON UPDATE CASCADE;

--
-- Constraints for table `student_promo`
--
ALTER TABLE `student_promo`
  ADD CONSTRAINT `student_promo_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `student_main` (`StudentID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `student_promo_ibfk_2` FOREIGN KEY (`PromoClass`) REFERENCES `sub_class_main` (`SubClassID`) ON UPDATE CASCADE;

--
-- Constraints for table `subclass_form_master`
--
ALTER TABLE `subclass_form_master`
  ADD CONSTRAINT `subclass_form_master_ibfk_1` FOREIGN KEY (`SubClassID`) REFERENCES `sub_class_main` (`SubClassID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `subclass_form_master_ibfk_2` FOREIGN KEY (`StaffID`) REFERENCES `staff_main` (`StaffID`) ON UPDATE CASCADE;

--
-- Constraints for table `subclass_master`
--
ALTER TABLE `subclass_master`
  ADD CONSTRAINT `subclass_master_ibfk_1` FOREIGN KEY (`StaffID`) REFERENCES `staff_main` (`StaffID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `subclass_master_ibfk_2` FOREIGN KEY (`SubClassID`) REFERENCES `sub_class_main` (`SubClassID`);

--
-- Constraints for table `subject_main`
--
ALTER TABLE `subject_main`
  ADD CONSTRAINT `subject_main_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `subj_category` (`CategoryID`) ON UPDATE CASCADE;

--
-- Constraints for table `sub_class_main`
--
ALTER TABLE `sub_class_main`
  ADD CONSTRAINT `sub_class_main_ibfk_1` FOREIGN KEY (`CourseID`) REFERENCES `class_main` (`ClassID`) ON UPDATE CASCADE;

--
-- Constraints for table `temp_general_student_billing`
--
ALTER TABLE `temp_general_student_billing`
  ADD CONSTRAINT `temp_general_student_billing_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `student_main` (`StudentID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `temp_general_student_billing_ibfk_2` FOREIGN KEY (`SubClassID`) REFERENCES `sub_class_main` (`SubClassID`) ON UPDATE CASCADE;

--
-- Constraints for table `temp_staff_class_subj_mapp`
--
ALTER TABLE `temp_staff_class_subj_mapp`
  ADD CONSTRAINT `temp_staff_class_subj_mapp_ibfk_1` FOREIGN KEY (`StaffID`) REFERENCES `staff_main` (`StaffID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `temp_staff_class_subj_mapp_ibfk_2` FOREIGN KEY (`ClassID`) REFERENCES `sub_class_main` (`SubClassID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `temp_staff_class_subj_mapp_ibfk_3` FOREIGN KEY (`SubjectID`) REFERENCES `subject_main` (`SubjectID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `temp_student_admission_fee`
--
ALTER TABLE `temp_student_admission_fee`
  ADD CONSTRAINT `temp_student_admission_fee_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `student_main` (`StudentID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `t_date`
--
ALTER TABLE `t_date`
  ADD CONSTRAINT `t_date_ibfk_1` FOREIGN KEY (`ActiveDate`) REFERENCES `all_date` (`TDate`) ON UPDATE CASCADE;

--
-- Constraints for table `user_auth`
--
ALTER TABLE `user_auth`
  ADD CONSTRAINT `user_auth_ibfk_1` FOREIGN KEY (`Username`) REFERENCES `kaina` (`ID`) ON UPDATE CASCADE;

--
-- Constraints for table `user_expense_petty_cash`
--
ALTER TABLE `user_expense_petty_cash`
  ADD CONSTRAINT `user_expense_petty_cash_ibfk_1` FOREIGN KEY (`Username`) REFERENCES `kaina` (`ID`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
