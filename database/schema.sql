CREATE TABLE IF NOT EXISTS sentiment_analysis (
    id INT PRIMARY KEY AUTO_INCREMENT,
    article_id VARCHAR(255) NOT NULL,
    title TEXT NOT NULL,
    sentiment_score FLOAT NOT NULL,
    sentiment_polarity VARCHAR(20) NOT NULL,
    emotions JSON,
    analyzed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_article_id ON sentiment_analysis(article_id);
CREATE INDEX IF NOT EXISTS idx_analyzed_at ON sentiment_analysis(analyzed_at);
