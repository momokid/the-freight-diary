# The Freight Diary

### Intelligent Logistics & Accounting Platform

Built by **AnwarVerse Ltd**

---

## Overview

The Freight Diary is a modern logistics and accounting platform designed for freight forwarding and container management operations.

This system integrates:

- Consignment lifecycle management
- Container tracking
- House BL (LCL) and Main BL (FCL) handling
- Double-entry accounting
- Branch-based data isolation
- Audit-ready transaction logging
- Future AI-powered automation

The goal of this rebuild is to create a scalable, secure, and user-friendly ERP-grade platform.

---

## Core Domain Concepts

### 1. Consignment

A consignment represents a Bill of Lading (BL) and acts as the aggregate root of operations.

There are two types:

#### A. Full Container Load (FCL)

- Uses `MainBL`
- Single consignee
- Revenue and expenses tied to MainBL

#### B. Less than Container Load (LCL / Part Container)

- Multiple consignees per container
- Each consignee receives a generated `HouseBL`
- Revenue and expense tracked per HouseBL
- Operational tracking tied to MainBL container

---

### 2. Container Management

Containers are created at the time of consignment creation.

Each container includes:

- Seal number
- Container number
- Weight
- Handling cost
- Gate-out date
- Return date
- Status tracking

---

### 3. Accounting Engine

The system implements full double-entry accounting.

Components:

- Journal (Debit/Credit entries)
- Ledger Accounts (Chart of Accounts)
- Profit & Loss tracking
- Receipt linkage
- Branch-based accounting isolation

All financial transactions must balance.

---

## Architecture Philosophy

This application follows a layered architecture:
