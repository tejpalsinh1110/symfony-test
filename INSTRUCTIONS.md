# Simple E-commerce App Test

## Overview

In this test, you will build a simplified e-commerce application using Symfony 6.4. The focus is on demonstrating your proficiency with Symfony.

## Project Structure

#### Implement the following page structure:

1. A home Page (`/home`)
2. Product Listing Page (`/products`) with pagination
3. Product Detail Page (`/products/[id]`)

#### API Endpoints to Implement 

- `GET /api/products`: Get all products (paginated)
- `GET /api/products/[id]`: Get a single product


## Core Requirements

1. **Product information**
    - Ensure that product data can be stored in the database.
    - Products contain minimum the following fields
        - Title
        - Short description (longtext)
        - Price excl vat
        - Category (from a fixed list)
        - Extra fields can be added if necessary
        - An optional field to store a product image can be added
    - Add a screen to add product information
2. **Product Listing Page**
    - Create a paginated table-based overview of products
    - The overview should show the title and price and allow sorting on both
    - An optional enhancement is filtering the overview on category
3. **Product Detail Page**
    - Create a detail page that shows the product
4. **API**
    - Create an API to fetch product information
    - This can be done using any preferred system (within the Symfony application)
5. **Additonal Information**
    - Ensure there is a minimal styling on the system using any preferred design system
    - The system needs to be responsive and work well on all device sizes
    - Implement a simple styling and responsive design that works well on mobile devices

## Technical Requirements

1. **Coding standards**
    - Ensure PHPStan continues to give a green report
2. **Testing (Bonus)**
    - Write unit tests for at least two key components using PHPUnit


## Submission Guidelines

1. Initialize a local git repository, create a feature branch and make your changes
2. Commit your code regularly with clear, concise commit messages
3. Create a README.md with:
    - Setup and run instructions
    - An overview of the features you've implemented
    - Any assumptions or design decisions you made
    - What you would do differently or add if you had more time
4. Package the repository in a zip and send it through.


## Time Limit

You have 1 day to complete this test. Focus on demonstrating your skills in Symfony and overall application architecture. It's okay if you don't complete every feature - we're more interested in seeing quality code and good decision-making.

Good luck! We're looking forward to seeing your implementation.