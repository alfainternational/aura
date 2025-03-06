# AURA System

AURA is a comprehensive digital platform that integrates various services including digital wallet, messaging, e-commerce, and AI capabilities.

## Project Structure

The project is divided into three main components:

### Backend (Laravel)
- Located in `/backend`
- PHP Laravel framework
- RESTful API endpoints
- Database management
- Business logic implementation

### Frontend (Vue.js)
- Located in `/frontend`
- Vue.js 3 framework
- Modern responsive UI
- State management with Vuex
- Component-based architecture

### Mobile (React Native)
- Located in `/mobile`
- Cross-platform mobile application
- Native Android and iOS support
- Shared business logic

## Setup Instructions

### Backend Setup
1. Navigate to `/backend`
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure
4. Run `php artisan key:generate`
5. Run `php artisan migrate`

### Frontend Setup
1. Navigate to `/frontend`
2. Run `npm install`
3. Run `npm run serve` for development

### Mobile Setup
1. Navigate to `/mobile`
2. Run `npm install`
3. Follow platform-specific setup instructions in mobile README

## Documentation
Detailed documentation can be found in the `/docs` directory.
