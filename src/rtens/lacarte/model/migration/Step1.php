<?php
namespace rtens\lacarte\model\migration;

class Step1 extends BaseStep {

    public function up() {
        $this->db->execute("CREATE TABLE IF NOT EXISTS groups (
                    'id' INTEGER NOT NULL,
                    'name' TEXT(255),
                    'adminEmail' TEXT(255),
                    'adminPassword' TEXT(255),
                    PRIMARY KEY ('id'));");

        $this->db->execute("CREATE TABLE IF NOT EXISTS users (
                    'id' INTEGER NOT NULL,
                    'groupId' INTEGER NOT NULL,
                    'name' TEXT(255),
                    'email' TEXT(255),
                    'key' TEXT(32),
                    PRIMARY KEY ('id'),
                    CONSTRAINT unique_email UNIQUE ('email'),
                    CONSTRAINT unique_key UNIQUE ('key'));");

        $this->db->execute("CREATE TABLE IF NOT EXISTS orders (
                    'id' INTEGER NOT NULL,
                    'groupId' INTEGER NOT NULL,
                    'name' TEXT(255),
                    'deadline' TEXT(32),
                    PRIMARY KEY ('id'));");

        $this->db->execute("CREATE TABLE IF NOT EXISTS menus (
                    'id' INTEGER NOT NULL,
                    'orderId' INTEGER NOT NULL,
                    'date' TEXT(255),
                    PRIMARY KEY ('id'));");

        $this->db->execute("CREATE TABLE IF NOT EXISTS dishes (
                    'id' INTEGER NOT NULL,
                    'menuId' INTEGER NOT NULL,
                    'text' TEXT(500),
                    PRIMARY KEY ('id'));");
    }

    public function down() {
        $this->db->execute("DROP TABLE groups;");
        $this->db->execute("DROP TABLE users;");
        $this->db->execute("DROP TABLE orders;");
        $this->db->execute("DROP TABLE menus;");
        $this->db->execute("DROP TABLE dishes;");
    }
}
