# News Sentiment Analysis Dashboard

A simple web application for analyzing the sentiment of news articles fetched from an external API and displaying the results in a dashboard.

## Features

*   Fetches news articles from a configurable API endpoint.
*   Analyzes the sentiment of each article using the PHPInsight library.
*   Provides a dashboard view with key metrics:
    *   Total number of articles analyzed.
    *   Average sentiment score across all articles.
    *   Sentiment distribution (Positive, Neutral, Negative) using a pie chart.
    *   Sentiment trend over time using a line chart.
    *   A list of analyzed articles with their sentiment scores and links.
    *   Highlights top positive and negative headlines.
*   Modern and responsive user interface using HTML, CSS, and JavaScript.
*   Interactive charts powered by Chart.js.
*   Option to refresh data.
*   Dark/Light theme toggle.

## Technologies Used

*   **Backend:** PHP
*   **Frontend:** HTML, CSS, JavaScript
*   **PHP Libraries:**
    *   Composer for dependency management.
    *   jwhennessey/phpinsight for sentiment analysis.
    *   guzzlehttp/guzzle for making HTTP requests to the news API.
*   **JavaScript Libraries:**
    *   Chart.js for data visualization.

## Setup Instructions

1.  **Prerequisites:**
    *   PHP (>= 7.4)
    *   Composer
    *   A web server (like Apache or Nginx) with PHP support.
    *   Ensure `allow_url_fopen` is enabled in your php.ini if you encounter issues fetching the API.

2.  **Clone the repository:**

    ```bash
    git clone <repository_url>
    cd <repository_directory>
    ```

3.  **Install PHP dependencies:**

    ```bash
    composer install
    ```

4.  **Configure the News API Endpoint:**

    The application fetches news from an API endpoint configured in `classes/Services/NewsAPI.php`. Currently, it's hardcoded. You might want to make this configurable, perhaps via a configuration file or environment variable if needed for a production setup.

    ```php
    // classes/Services/NewsAPI.php
    private $baseUrl = "https://newstimes.share.zrok.io"; // <-- Update this if your API is different
    ```

5.  **Set up your web server:**

    Configure your web server to serve the project directory. Ensure that `.php` files are processed by the PHP interpreter.

## How to Run

1.  Start your web server.
2.  Open your web browser and navigate to the URL where the project is hosted (e.g., `http://localhost/` or `http://your-domain.com/`).

The dashboard will load and fetch data from the API to display the sentiment analysis results.

## Project Structure

```
.
├── api/
│   └── sentiment_analysis.php  # Backend API endpoint for sentiment analysis
├── assets/
│   ├── css/
│   │   └── style.css           # Custom CSS for styling
│   └── js/
│       └── script.js         # Frontend JavaScript for dashboard logic and API interaction
├── classes/
│   ├── Config.php              # Handles application configuration loading
│   ├── NewsAnalyzer.php        # Orchestrates news fetching and sentiment analysis
│   ├── SentimentAnalyzer.php   # Performs sentiment analysis using PHPInsight
│   └── Services/
│       └── NewsAPI.php       # Handles interaction with the external News API
├── config/
│   └── app.php               # Application configuration settings
├── vendor/                   # Composer dependencies
├── index.html                # Frontend dashboard HTML structure
└── composer.json             # Composer configuration file
```

## Potential Enhancements

*   Make the News API endpoint and other settings configurable via environment variables or a dedicated config file.
*   Implement caching for API responses to improve performance and reduce external API calls.
*   Add more sophisticated sentiment analysis features or allow selecting different sentiment models.
*   Improve error handling and user feedback in the frontend.
*   Add filtering and sorting options for the article list.
*   Implement pagination for a large number of articles. 