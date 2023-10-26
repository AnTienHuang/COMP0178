CREATE DATABASE Auction;
# DROP DATABASE Auction;

USE Auction;

CREATE TABLE Users(
userID int NOT NULL,
firstName varchar(20) NOT NULL,
lastName varchar(20) NOT NULL,
password varchar(20) NOT NULL,
email varchar(20) NOT NULL,
userType varchar(6) NOT NULL);

CREATE Table item(
itemID int NOT NULL,
sellerID int NOT NULL,
title varchar(255) NOT NULL,
startTime datetime NOT NULL,
endTime datetime NOT NULL,
startingPice double NOT NULL,
reversePrice double,
status varchar(10) NOT NULL,
category varchar(20) NOT NULL);

CREATE TABLE Bid(
bidID int NOT NULL,
buyerID int NOT NULL,
itemID int NOT NULL,
price double NOT NULL,
status varchar(10) NOT NULL,
bidDate datetime NOT NULL);

