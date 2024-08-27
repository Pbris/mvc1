# MVC Course Repo

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Pbris/mvc1/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/Pbris/mvc1/?branch=main)
[![Code Coverage](https://scrutinizer-ci.com/g/Pbris/mvc1/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/Pbris/mvc1/?branch=main)
[![Build Status](https://scrutinizer-ci.com/g/Pbris/mvc1/badges/build.png?b=main)](https://scrutinizer-ci.com/g/Pbris/mvc1/build-status/main)
## Introduction
Welcome to my Symfony-based website. This project is made to show various features in course MVC (course code DV1608) at BTH university. Below, you’ll find description about the content, and instructions on how to get started.

## Description
The repo contains the code for the course’s all 7 parts. In the course we’ve used PHP in the framework Symfony to learn the MVC basics, object oriented PHP, database handling and unit testing. The 7 parts in the course were the following:

- Framework
- Object Oriented Programming
- Applications in Symfony
- Unit testing
- ORM/Database-connections
- Automated testing
- Project and examination (I developed a Blackjack game as final project)

![Composer](public/img/logo-composer-transparent5.png)

## Getting Started
1. **Clone the Repository:**
    ```
    git clone https://github.com/Pbris/mvc1.git
    ```
   - Navigate to the project directory:
     ```
     cd me/report
     ```

2. **Run the Web Server:**
    ```
    # Have Composer installed and then run the build and its dependencies
    npm run build

    # Have PHP 8.2.4 installed and then run
    php -S localhost:8888 -t public
    ```
   Access the website in your browser at [http://localhost:8888](http://localhost:8888).


## Website Routes
- **Home (/presentation)**
  - Provides a presentation of the creator.

- **About (/about)**
  - Describes the MVC course and its purpose.
  - Links to the course’s Git repo.
  - Provides another link to this Git repo.

- **Report (/report)**
  - Course reports (redovisningstexter) for each kmom.
  - Direct links to specific kmom reports.

- **Lucky (/lucky)**
  - Stylish and perhaps a bit “crazy.”

- **Api landing page (/api)**
  - JSON API routes.

- **Session page (/session)**
  - Show and destroy session, optionally.

- **Card game (/card)**
  - Base for card game.

- **Blackjack (/game)**
  - Initial basic Blackjack game, 1 hand vs dealer.

- **Library (/library)**
  - Simple library database using Doctrine.


- **Metrics (/metrics)**
  - Code quality analysis.

- **Project (/proj)**
  - Final project, more advanced Blackjack game. Also including new navbar with routes for JSON API related to project (/api), and description of project (/doc).
