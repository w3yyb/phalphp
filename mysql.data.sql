 

--
-- Table structure for table `aclaccess`
--

DROP TABLE IF EXISTS `aclaccess`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aclaccess` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resourceid` int(11) NOT NULL COMMENT '资源id',
  `roleid` int(11) DEFAULT NULL COMMENT '角色id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8 COMMENT='acl_权限表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `username` varchar(30) NOT NULL COMMENT '用户名',
  `password` varchar(100) NOT NULL COMMENT '密码',
  `logintime` varchar(20) DEFAULT NULL COMMENT '登录时间',
  `regtime` varchar(20) NOT NULL COMMENT '创建时间',
  `adminname` varchar(30) NOT NULL COMMENT '在线管理员名称',
  `info` varchar(500) DEFAULT NULL,
  `roleid` int(11) DEFAULT NULL COMMENT '角色id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COMMENT='管理员信息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api`
--

DROP TABLE IF EXISTS `api`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api` (
  `client_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `public_id` char(64) NOT NULL DEFAULT '',
  `private_key` char(64) NOT NULL DEFAULT '',
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  PRIMARY KEY (`client_id`),
  UNIQUE KEY `private_key` (`private_key`),
  UNIQUE KEY `public_id` (`public_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `app`
--

DROP TABLE IF EXISTS `app`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app` (
  `app_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增',
  `username` varchar(30) NOT NULL COMMENT '用户名',
  `private_key` char(64) NOT NULL COMMENT 'key值',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态:0正常1删除',
  `adminname` varchar(30) DEFAULT NULL COMMENT '在线管理员',
  `addtime` varchar(30) DEFAULT NULL,
  `info` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`app_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='客户端表';
/*!40101 SET character_set_client = @saved_cs_client */;

 

 

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `ip` char(30) DEFAULT NULL,
  `addtime` datetime NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` varchar(200) NOT NULL,
  `url` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=351 DEFAULT CHARSET=utf8 COMMENT='操作日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

 

 
 

 
 
 

--
-- Table structure for table `operations`
--

DROP TABLE IF EXISTS `operations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `operations` (
  `operationid` int(11) NOT NULL AUTO_INCREMENT COMMENT '操作id',
  `name` varchar(100) NOT NULL COMMENT '操作名称',
  `resourceid` int(11) NOT NULL,
  `operationinfo` varchar(30) DEFAULT NULL COMMENT '操作描述',
  PRIMARY KEY (`operationid`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='acl_操作表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `queryerror`
--

DROP TABLE IF EXISTS `queryerror`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `queryerror` (
  `error_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `query` text,
  `file` varchar(1024) DEFAULT '',
  `line` int(10) unsigned DEFAULT NULL,
  `error_string` varchar(1024) DEFAULT '',
  `error_no` int(10) unsigned DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `execution_script` varchar(1024) DEFAULT '',
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_address` varchar(16) DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`error_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resources`
--

DROP TABLE IF EXISTS `resources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resources` (
  `resourceid` int(11) NOT NULL AUTO_INCREMENT COMMENT '资源id',
  `name` varchar(100) NOT NULL COMMENT '资源名称',
  `resourceinfo` varchar(30) DEFAULT NULL COMMENT '资源描述',
  PRIMARY KEY (`resourceid`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='acl_资源表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `roleid` int(11) NOT NULL AUTO_INCREMENT COMMENT '角色id',
  `rolename` varchar(30) NOT NULL COMMENT '角色名称',
  `roleinfo` varchar(30) NOT NULL COMMENT '角色说明',
  PRIMARY KEY (`roleid`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='acl_角色表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `runtimeerror`
--

DROP TABLE IF EXISTS `runtimeerror`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `runtimeerror` (
  `error_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(2048) NOT NULL DEFAULT '',
  `file` varchar(1024) DEFAULT '',
  `line` int(10) unsigned DEFAULT NULL,
  `error_type` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` datetime DEFAULT NULL,
  `server_name` varchar(100) DEFAULT NULL,
  `execution_script` varchar(1024) NOT NULL DEFAULT '',
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_address` varchar(16) DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`error_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `setting`
--

DROP TABLE IF EXISTS `setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '变量名',
  `value` varchar(255) DEFAULT NULL COMMENT '变量值',
  `info` varchar(255) DEFAULT NULL COMMENT '说明',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

 

 

--
-- Table structure for table `task`
--

DROP TABLE IF EXISTS `task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task` (
  `task_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `script_name` varchar(1024) NOT NULL DEFAULT '',
  `params` varchar(1024) DEFAULT '',
  `server_name` varchar(30) DEFAULT '',
  `server_user` varchar(30) DEFAULT '',
  `start_time` datetime DEFAULT NULL,
  `stop_time` datetime DEFAULT NULL,
  `state` enum('RUNNING','SUCCESSFUL','FAILED') DEFAULT 'RUNNING',
  `exit_status` int(10) unsigned DEFAULT NULL,
  `stdout` text,
  `stderr` text,
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

 

-- Dump completed on 2016-11-23 18:33:55
