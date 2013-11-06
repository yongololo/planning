-- MySQL dump 10.13  Distrib 5.1.72, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: GestPlanBx3
-- ------------------------------------------------------
-- Server version	5.1.72-0ubuntu0.10.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Observations`
--

DROP TABLE IF EXISTS `Observations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Observations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Date` date NOT NULL DEFAULT '0000-00-00',
  `Id-horaire` int(11) NOT NULL,
  `Observation` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2436 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Observations`
--

--
-- Table structure for table `conges`
--

DROP TABLE IF EXISTS `conges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_perso` varchar(120) NOT NULL DEFAULT '',
  `date_depart` date NOT NULL DEFAULT '0000-00-00',
  `dm_depart` varchar(120) NOT NULL DEFAULT '',
  `date_fin` date NOT NULL DEFAULT '0000-00-00',
  `dm_fin` varchar(120) NOT NULL DEFAULT '',
  `type` varchar(10) NOT NULL DEFAULT 'conges',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1709 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conges`
--

--
-- Table structure for table `conges_permanent`
--

DROP TABLE IF EXISTS `conges_permanent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conges_permanent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_perso` varchar(120) NOT NULL DEFAULT '',
  `jour` varchar(120) NOT NULL DEFAULT '',
  `tranche` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conges_permanent`
--

--
-- Table structure for table `groupes`
--

DROP TABLE IF EXISTS `groupes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groupes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(50) NOT NULL,
  `sect` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groupes`
--

LOCK TABLES `groupes` WRITE;
/*!40000 ALTER TABLE `groupes` DISABLE KEYS */;
INSERT INTO `groupes` VALUES (1,'Renseignements','1,4,8'),(2,'Prêt','2,5,9,11'),(3,'Tutorat','3,10'),(4,'Accueil','7'),(5,'Communication','6');
/*!40000 ALTER TABLE `groupes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horaires`
--

DROP TABLE IF EXISTS `horaires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `horaires` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `heures` varchar(120) NOT NULL DEFAULT '',
  `temps` varchar(120) NOT NULL DEFAULT '',
  `fermeture` varchar(120) NOT NULL,
  `midi` varchar(120) NOT NULL DEFAULT '',
  `fermeture_bool` tinyint(1) NOT NULL,
  `type_sem` varchar(12) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horaires`
--

LOCK TABLES `horaires` WRITE;
/*!40000 ALTER TABLE `horaires` DISABLE KEYS */;
INSERT INTO `horaires` VALUES (1,'Toute la journée','','','',0,''),(2,'Le matin','','','',0,''),(3,'Après-midi','','','',0,''),(4,'08h30 - 10h00','90','','',0,''),(5,'10h00 - 11h30','90','','',0,''),(6,'11h30 - 13h00','90','','',0,''),(7,'13h00 - 14h30','90','','',0,''),(8,'14h30 - 16h00','90','','',0,''),(9,'16h00 - 17h30','90','','',0,''),(10,'17h30 - 19h00','90','','',1,''),(11,'09h00 - 12h30','210','','',1,''),(12,'09h00 - 11h00','120','','',0,''),(13,'11h00 - 13h00','120','','',0,''),(14,'13h00 - 15h00','120','','',0,''),(15,'15h00 - 17h00','120','','',0,'');
/*!40000 ALTER TABLE `horaires` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jours_horaires`
--

DROP TABLE IF EXISTS `jours_horaires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jours_horaires` (
  `jour` varchar(50) NOT NULL,
  `id_horaire` int(11) NOT NULL,
  `id_horaire_ete` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jours_horaires`
--

LOCK TABLES `jours_horaires` WRITE;
/*!40000 ALTER TABLE `jours_horaires` DISABLE KEYS */;
INSERT INTO `jours_horaires` VALUES ('Lundi',4,0),('Lundi',5,0),('Lundi',6,0),('Lundi',7,0),('Lundi',8,0),('Lundi',9,0),('Lundi',10,0),('Mardi',4,0),('Mardi',5,0),('Mardi',6,0),('Mardi',7,0),('Mardi',8,0),('Mardi',9,0),('Mardi',10,0),('Mercredi',4,0),('Mercredi',5,0),('Mercredi',6,0),('Mercredi',7,0),('Mercredi',8,0),('Mercredi',9,0),('Mercredi',10,0),('Jeudi',4,0),('Jeudi',5,0),('Jeudi',6,0),('Jeudi',7,0),('Jeudi',8,0),('Jeudi',9,0),('Jeudi',10,0),('Vendredi',4,0),('Vendredi',5,0),('Vendredi',6,0),('Vendredi',7,0),('Vendredi',8,0),('Vendredi',9,0),('Vendredi',10,0),('Samedi',11,0),('Lundi',0,12),('Lundi',0,13),('Lundi',0,14),('Lundi',0,15),('Mardi',0,12),('Mardi',0,13),('Mardi',0,14),('Mardi',0,15),('Mercredi',0,12),('Mercredi',0,13),('Mercredi',0,14),('Mercredi',0,15),('Jeudi',0,12),('Jeudi',0,13),('Jeudi',0,14),('Jeudi',0,15),('Vendredi',0,12),('Vendredi',0,13),('Vendredi',0,14),('Vendredi',0,15);
/*!40000 ALTER TABLE `jours_horaires` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mouchard`
--
--
-- Table structure for table `perso_groupe`
--

DROP TABLE IF EXISTS `perso_groupe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perso_groupe` (
  `id_perso` int(11) NOT NULL,
  `id_groupe` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perso_groupe`
--

--
-- Table structure for table `personnel`
--

DROP TABLE IF EXISTS `personnel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personnel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(120) NOT NULL DEFAULT '',
  `prenom` varchar(120) NOT NULL DEFAULT '',
  `sections` varchar(120) NOT NULL DEFAULT '',
  `section_principale` int(11) NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `pass` varchar(50) NOT NULL,
  `autogestion` tinyint(1) NOT NULL,
  `mail` varchar(120) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=119 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personnel`
--

--
-- Table structure for table `planning`
--

DROP TABLE IF EXISTS `planning`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `planning` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `num_semaine` int(11) NOT NULL DEFAULT '0',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `id_section` varchar(120) NOT NULL DEFAULT '',
  `id_perso` varchar(120) NOT NULL DEFAULT '',
  `id_horaire` varchar(120) NOT NULL DEFAULT '',
  `position` varchar(120) NOT NULL DEFAULT '',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=77252 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `planning`
--

--
-- Table structure for table `planning_type`
--

DROP TABLE IF EXISTS `planning_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `planning_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_semaine_type` int(11) NOT NULL,
  `num_jour` int(11) DEFAULT NULL,
  `id_section` varchar(120) NOT NULL DEFAULT '',
  `id_perso` varchar(120) NOT NULL DEFAULT '',
  `id_horaire` varchar(120) NOT NULL DEFAULT '',
  `position` varchar(120) NOT NULL DEFAULT '',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=87674 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `planning_type`
--

LOCK TABLES `planning_type` WRITE;
/*!40000 ALTER TABLE `planning_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `planning_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `postit`
--

--
-- Table structure for table `presence`
--

DROP TABLE IF EXISTS `presence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `presence` (
  `id_perso` int(11) NOT NULL,
  `jour` text NOT NULL,
  `debut` text NOT NULL,
  `fin` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presence`
--

--
-- Table structure for table `previsions_conges`
--

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(120) NOT NULL DEFAULT '',
  `max_pers` varchar(120) NOT NULL DEFAULT '',
  `couleur` varchar(120) NOT NULL DEFAULT '',
  `class` varchar(40) NOT NULL,
  `r` varchar(120) NOT NULL DEFAULT '',
  `g` varchar(120) NOT NULL DEFAULT '',
  `b` varchar(120) NOT NULL DEFAULT '',
  `ordre` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` VALUES (1,'Rens. (1er)','1','#66CCFF','etage_1','204','255','255',1),(2,'Prêt (1er)','1','#66CCFF','etage_1','204','255','255',2),(3,'Tutorat (1er)','1','#66CCFF','etage_1','204','255','255',3),(4,'Rens. (2ème)','1','#CC99FF','etage_2','204','153','255',4),(5,'Prêt (2ème)','1','#CC99FF','etage_2','204','153','255',5),(6,'Comm. (2ème)','1','#CC99FF','etage_2','204','153','255',6),(7,'Accueil','2','#CCFFCC','etage_0','204','255','204',11),(8,'Rens. (3ème)','1','#FFCC99','etage_3','255','204','153',8),(9,'Prêt (3ème)','1','#FFCC99','etage_3','255','204','153',9),(10,'Tutorat (3ème)','1','#FFCC99','etage_3','255','204','153',10),(11,'Recotation Dewey','2','','etage_2','','','',12);
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `semaines_ete`
--

DROP TABLE IF EXISTS `semaines_ete`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `semaines_ete` (
  `annee` int(11) NOT NULL,
  `num_sem` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `semaines_ete`
--

LOCK TABLES `semaines_ete` WRITE;
/*!40000 ALTER TABLE `semaines_ete` DISABLE KEYS */;
INSERT INTO `semaines_ete` VALUES (2012,27),(2012,28),(2012,29),(2012,30),(2012,31),(2012,32),(2012,33),(2012,34),(2012,35),(2013,27),(2013,28),(2013,29),(2013,30),(2013,31),(2013,32),(2013,33),(2013,34),(2013,35),(2013,36),(2013,37);
/*!40000 ALTER TABLE `semaines_ete` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `semaines_types`
--

DROP TABLE IF EXISTS `semaines_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `semaines_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `semaines_types`
--
--
-- Table structure for table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateurs` (
  `id_user` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL DEFAULT '',
  `pass` varchar(50) NOT NULL DEFAULT '',
  `nom` varchar(50) NOT NULL DEFAULT '',
  `prenom` varchar(50) NOT NULL DEFAULT '',
  `privilege` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_user`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateurs`
--

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
