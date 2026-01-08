# ADR-0001: Laravel Framework with Vite/Tailwind Tooling

## Context

The application is a browser-based MVP that requires rapid development of CRUD workflows, authentication, and server-rendered views. The codebase is structured around Laravel conventions and uses the Laravel ecosystem for routing, controllers, services, and Eloquent ORM. Frontend assets are bundled with Vite and styled with Tailwind CSS.

## Decision

Adopt **Laravel** as the primary backend framework and **Vite + Tailwind CSS** for frontend asset tooling.

## Consequences

- Provides a productive MVC structure with built-in authentication scaffolding and database migrations.
- Enables fast iteration on UI assets with modern tooling.
- Establishes clear conventions for controllers, services, and models to keep the codebase organized.
