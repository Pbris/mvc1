# My Symfony Website

## Introduction
Welcome to my Symfony-based website. This project is made to show various features in course MVC at BTH university. Below, you’ll find instructions on how to get started.

![Composer](.img/logo-composer-transparent5.png)

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
- **Home (/)**
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
