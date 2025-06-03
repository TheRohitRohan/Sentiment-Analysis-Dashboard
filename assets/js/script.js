// Chart instances
let sentimentChart = null;
let timeSeriesChart = null;

// DOM Elements
const refreshBtn = document.getElementById('refreshBtn');
const themeToggle = document.getElementById('themeToggle');
const searchInput = document.getElementById('searchInput');
const sentimentFilter = document.getElementById('sentimentFilter');
const articleTableBody = document.getElementById('articleTableBody');
const totalArticlesSpan = document.getElementById('totalArticles');
const averageSentimentSpan = document.getElementById('averageSentiment');
const lastUpdatedSpan = document.getElementById('lastUpdated');
const positiveCountSpan = document.getElementById('positiveCount');
const neutralCountSpan = document.getElementById('neutralCount');
const negativeCountSpan = document.getElementById('negativeCount');
const topPositiveList = document.getElementById('topPositiveList');
const topNegativeList = document.getElementById('topNegativeList');
const latestArticleContent = document.getElementById('latestArticleContent');

// Store raw data
let allArticles = [];

// Theme handling
function toggleTheme() {
    const isDark = document.body.getAttribute('data-theme') === 'dark';
    document.body.setAttribute('data-theme', isDark ? 'light' : 'dark');
    themeToggle.innerHTML = `<span class="icon">${isDark ? '🌙' : '☀️'}</span>`;
    localStorage.setItem('theme', isDark ? 'light' : 'dark');
    // Re-render charts to apply theme changes
    renderDashboard();
}

// Initialize theme
const savedTheme = localStorage.getItem('theme') || 'light';
document.body.setAttribute('data-theme', savedTheme);
themeToggle.innerHTML = `<span class="icon">${savedTheme === 'dark' ? '☀️' : '🌙'}</span>`;

// Event Listeners
themeToggle.addEventListener('click', toggleTheme);
refreshBtn.addEventListener('click', fetchData);
searchInput.addEventListener('input', renderDashboard);
sentimentFilter.addEventListener('change', renderDashboard);

// Fetch data from API
async function fetchData() {
    try {
        // Add loading state
        document.body.classList.add('loading');
        
        const response = await fetch('api/sentiment_analysis.php');
        const data = await response.json();
        
        if (data.status === 'success' && data.data && data.data.full_analysis) {
            allArticles = data.data.full_analysis;
            renderDashboard();
        } else {
            throw new Error(data.message || 'Failed to fetch data');
        }
    } catch (error) {
        console.error('Error fetching data:', error);
        alert('Failed to fetch data. Please try again.');
    } finally {
        // Remove loading state
        document.body.classList.remove('loading');
    }
}

// Render the entire dashboard
function renderDashboard() {
    const filteredArticles = filterArticles(allArticles);
    const dashboardData = calculateDashboardData(filteredArticles);
    
    updateSummaryCards(dashboardData.summary);
    updateSentimentChart(dashboardData.sentimentDistribution);
    updateTimeSeriesChart(dashboardData.timeSeries);
    updateArticleTable(filteredArticles);
    updateSentimentCountBoxes(dashboardData.sentimentDistribution);
    updateTopHeadlines(allArticles); // Use allArticles for top headlines regardless of filter
    updateLatestArticle(allArticles); // Use allArticles for latest article regardless of filter
}

// Calculate dashboard data from articles
function calculateDashboardData(articles) {
    const totalArticles = articles.length;
    let totalSentimentScore = 0;
    const sentimentDistribution = { positive: 0, neutral: 0, negative: 0 };
    const timeSeries = { labels: [], data: [] };
    let lastUpdated = '-';
    
    // Sort articles by analyzed_at in ascending order for time series chart
    const sortedArticles = [...articles].sort((a, b) => new Date(a.analyzed_at) - new Date(b.analyzed_at));

    if (sortedArticles.length > 0) {
        sortedArticles.forEach(article => {
            totalSentimentScore += article.sentiment.score;
            sentimentDistribution[article.sentiment.polarity]++;
            timeSeries.labels.push(article.analyzed_at);
            timeSeries.data.push(article.sentiment.score);
        });
        
        lastUpdated = sortedArticles[sortedArticles.length - 1].analyzed_at; // Get the latest date
    }
    
    const averageSentiment = totalArticles > 0 ? totalSentimentScore / totalArticles : 0;
    
    return {
        summary: {
            totalArticles,
            averageSentiment,
            lastUpdated
        },
        sentimentDistribution,
        timeSeries
    };
}

// Update summary cards
function updateSummaryCards(summary) {
    totalArticlesSpan.textContent = summary.totalArticles;
    averageSentimentSpan.textContent = summary.averageSentiment.toFixed(2);
    lastUpdatedSpan.textContent = summary.lastUpdated;
}

// Update sentiment count boxes
function updateSentimentCountBoxes(distribution) {
    positiveCountSpan.textContent = distribution.positive;
    neutralCountSpan.textContent = distribution.neutral;
    negativeCountSpan.textContent = distribution.negative;
}

// Update sentiment distribution chart
function updateSentimentChart(data) {
    const ctx = document.getElementById('sentimentChart').getContext('2d');
    const isDarkMode = document.body.getAttribute('data-theme') === 'dark';
    const textColor = isDarkMode ? 'white' : '#64748b'; // Use text-secondary color
    
    if (sentimentChart) {
        sentimentChart.destroy();
    }
    
    sentimentChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Positive', 'Neutral', 'Negative'],
            datasets: [{
                data: [data.positive, data.neutral, data.negative],
                backgroundColor: [
                    '#43A047', // Positive (Green)
                    '#FB8C00', // Neutral (Orange)
                    '#E53935'  // Negative (Red)
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
             animation: { // Add animation
                animateScale: true,
                animateRotate: true
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: textColor }
                },
                 tooltip: { // Tooltip colors for dark mode
                    backgroundColor: isDarkMode ? '#333' : '',
                    titleColor: isDarkMode ? '#fff' : '',
                    bodyColor: isDarkMode ? '#eee' : ''
                }
            }
        }
    });
}

// Update time series chart
function updateTimeSeriesChart(data) {
    const ctx = document.getElementById('timeSeriesChart').getContext('2d');
     const isDarkMode = document.body.getAttribute('data-theme') === 'dark';
    const textColor = isDarkMode ? 'white' : '#64748b'; // Use text-secondary color
    const gridColor = isDarkMode ? '#4a5568' : '#e2e8f0'; // Use border color
    
    if (timeSeriesChart) {
        timeSeriesChart.destroy();
    }
    
    timeSeriesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Sentiment Score',
                data: data.data,
                borderColor: '#1E88E5', // Primary (Blue) for the line
                tension: 0.4,
                fill: false,
                pointRadius: 5, // Make points visible
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { // Add animation
                duration: 1000,
                easing: 'easeOutQuart'
            },
            scales: {
                x: {
                    ticks: {
                        color: textColor,
                        maxRotation: 45,
                        minRotation: 45,
                        callback: function(value, index, ticks) {
                            // Format label to show only time (HH:MM)
                            const date = new Date(data.labels[index]);
                            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        }
                    },
                    grid: { color: gridColor },
                     title: { // Add X-axis title
                        display: true,
                        text: 'Time (HH:MM)',
                        color: textColor
                    }
                },
                y: {
                    beginAtZero: false, // Allow negative values
                    max: 1,
                    min: -1,
                    ticks: { color: textColor }, // Apply text color
                    grid: { color: gridColor },
                     title: { // Add Y-axis title
                        display: true,
                        text: 'Sentiment Score (-1 to +1)',
                        color: textColor
                    },
                     // Add reference lines for sentiment zones
                    borderDash: [5, 5],
                    borderColors: {
                        0.3: '#43A047', // Positive boundary (Green)
                        
                        
                    },
                    
                }
            },
            plugins: {
                legend: {
                    display: false // Hide default legend
                },
                 tooltip: { // Tooltip improvements
                    backgroundColor: isDarkMode ? '#333' : '',
                    titleColor: isDarkMode ? '#fff' : '',
                    bodyColor: isDarkMode ? '#eee' : '',
                    callbacks: { // Customize tooltip content
                        title: function(context) {
                            const date = new Date(context[0].label);
                             return 'Time: ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        },
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.raw.toFixed(3); // Show score with precision
                            return label;
                        },
                        // You can add 'afterLabel' to potentially show headline, but requires mapping data points back to articles
                    }
                }
            }
        }
    });
}

// Update article table
function updateArticleTable(articles) {
    articleTableBody.innerHTML = '';
    
    articles.forEach(article => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><a href="${article.link}" target="_blank" rel="noopener noreferrer">${article.title}</a></td>
            <td><span class="sentiment-badge ${article.sentiment.polarity}">${article.sentiment.polarity}</span></td>
            <td>${article.sentiment.score.toFixed(2)}</td>
            <td>${article.sentiment.confidence.toFixed(2)}</td>
            <td>${article.analyzed_at}</td>
        `;
        articleTableBody.appendChild(row);
    });
}

// Update top positive and negative headlines
function updateTopHeadlines(articles) {
    // Sort articles by sentiment score
    const sortedArticles = [...articles].sort((a, b) => a.sentiment.score - b.sentiment.score);

    // Get top 5 positive and negative articles
    const topPositive = sortedArticles.slice(-5).reverse(); // Highest scores
    const topNegative = sortedArticles.slice(0, 5); // Lowest scores

    // Populate top positive headlines list
    topPositiveList.innerHTML = '';
    topPositive.forEach(article => {
        const listItem = document.createElement('li');
        listItem.innerHTML = `
            <div class="headline-title"><a href="${article.link}" target="_blank" rel="noopener noreferrer">${article.title}</a></div>
            <div class="headline-score positive">Score: ${article.sentiment.score.toFixed(3)}</div>
        `;
        topPositiveList.appendChild(listItem);
    });

    // Populate top negative headlines list
    topNegativeList.innerHTML = '';
    topNegative.forEach(article => {
        const listItem = document.createElement('li');
        listItem.innerHTML = `
            <div class="headline-title"><a href="${article.link}" target="_blank" rel="noopener noreferrer">${article.title}</a></div>
            <div class="headline-score negative">Score: ${article.sentiment.score.toFixed(3)}</div>
        `;
        topNegativeList.appendChild(listItem);
    });
}

// Update latest analyzed article
function updateLatestArticle(articles) {
    // Sort articles by analyzed_at in descending order to get the latest
    const sortedArticles = [...articles].sort((a, b) => new Date(b.analyzed_at) - new Date(a.analyzed_at));

    const latestArticle = sortedArticles.length > 0 ? sortedArticles[0] : null;

    latestArticleContent.innerHTML = '';
    if (latestArticle) {
        latestArticleContent.innerHTML = `
            <p><strong>Title:</strong> ${latestArticle.title}</p>
            <p><strong>Sentiment:</strong> <span class="sentiment-badge ${latestArticle.sentiment.polarity}">${latestArticle.sentiment.polarity}</span></p>
            <p><strong>Score:</strong> ${latestArticle.sentiment.score.toFixed(3)}</p>
            <p><strong>Analyzed At:</strong> ${latestArticle.analyzed_at}</p>
        `;
         latestArticleContent.classList.add('latest-article-content'); // Add class for styling
    } else {
        latestArticleContent.innerHTML = '<p>No articles analyzed yet.</p>';
         latestArticleContent.classList.remove('latest-article-content');
    }
}

// Filter articles based on search and sentiment filter
function filterArticles(articles) {
    const searchTerm = searchInput.value.toLowerCase();
    const sentimentValue = sentimentFilter.value;
    
    return articles.filter(article => {
        const title = article.title.toLowerCase();
        const polarity = article.sentiment.polarity;
        
        const matchesSearch = title.includes(searchTerm);
        const matchesSentiment = sentimentValue === 'all' || polarity === sentimentValue;
        
        return matchesSearch && matchesSentiment;
    });
}

// Initial data fetch
fetchData(); 