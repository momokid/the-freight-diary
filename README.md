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

### Design Principles

- No business logic inside controllers
- Service layer handles domain logic
- Repository layer handles database operations
- Authentication uses custom username-based login
- All critical actions logged
- Role-based access control (RBAC)

---

## Security

- Username-based authentication
- Bcrypt password hashing
- Login success/failure tracking
- Branch-based data filtering
- Super Admin global override
- CSRF protection
- Session regeneration on login

---

## Dashboard Features

- Mini collapsible sidebar
- Light/Dark theme toggle
- Role-aware navigation
- Welcome screen (first login only)
- KPI dashboard
- Notification system (future)
- AI assistant integration (future)

---

## AI Roadmap

Planned AI modules:

- OCR for BL document ingestion
- Financial anomaly detection
- Delay prediction engine
- Automated reporting assistant
- AI-powered operational search

---

## Technology Stack

- Laravel 12
- TailwindCSS
- Alpine.js
- MySQL
- Vite
- REST-ready architecture

---

## Testing Strategy

- Feature-based development
- Unit tests for services
- Integration tests for accounting transactions
- Authentication flow tests
- Branch isolation tests

Testing will be written after completion of each major feature.

---

## Git Workflow

- Feature-based commits
- Each feature pushed after:
    - Functional validation
    - Test coverage
    - Refactoring pass

Branch structure:

- main
- develop
- feature/<feature-name>

---

## Version

v2.0 – Prime Rebuild

---

© AnwarVerse Ltd
