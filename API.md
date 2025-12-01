# Kiasi Daily API Documentation

This document describes the API endpoints required for the Kiasi Daily Flutter application, with a focus on statistics and summary endpoints.


## Authentication
All protected endpoints require a Bearer token in the Authorization header:
```
Authorization: Bearer {token}
```

---

## Statistics & Summary Endpoints

### 1. Get Daily Summary
**Current Endpoint (Working)**
```
GET /summary/daily?date={date}
```

**Query Parameters:**
- `date` (required): Date in `YYYY-MM-DD` format

**Response:**
```json
{
  "date": "2024-01-15",
  "total_income": 50000.00,
  "total_expense": 25000.00,
  "transaction_count": 5,
  "income_count": 2,
  "expense_count": 3
}
```

---

### 2. Get Weekly Summaries
**Current Endpoint (Working)**
```
GET /summary/weekly?start_date={date}
```

**Query Parameters:**
- `start_date` (required): Start date of the week in `YYYY-MM-DD` format

**Response:**
```json
{
  "daily_summaries": [
    {
      "date": "2024-01-15",
      "total_income": 50000.00,
      "total_expense": 25000.00,
      "transaction_count": 5,
      "income_count": 2,
      "expense_count": 3
    },
    {
      "date": "2024-01-16",
      "total_income": 30000.00,
      "total_expense": 15000.00,
      "transaction_count": 3,
      "income_count": 1,
      "expense_count": 2
    }
    // ... up to 7 days
  ],
  "week_start": "2024-01-15",
  "week_end": "2024-01-21",
  "total_income": 200000.00,
  "total_expense": 100000.00
}
```

---

### 3. Get Expenses by Category
**Current Endpoint (Working)**
```
GET /summary/category?start_date={start}&end_date={end}
```

**Query Parameters:**
- `start_date` (required): Start date in `YYYY-MM-DD` format
- `end_date` (required): End date in `YYYY-MM-DD` format

**Response:**
```json
{
  "expenses_by_category": {
    "Food": 50000.00,
    "Transport": 30000.00,
    "Shopping": 20000.00,
    "Entertainment": 15000.00,
    "Bills": 25000.00,
    "Health": 10000.00,
    "Education": 5000.00,
    "Other": 5000.00
  },
  "total_expenses": 160000.00,
  "period_start": "2024-01-01",
  "period_end": "2024-01-31"
}
```

---

## 🚀 RECOMMENDED NEW ENDPOINTS (For Better Performance)

### 4. Get Monthly Summaries ⭐ **RECOMMENDED**
**New Endpoint (To Implement)**
```
GET /summary/monthly?year={year}&month={month}
```

**Purpose:** This endpoint will significantly improve the monthly statistics view by returning all daily summaries for a month in a single API call, instead of making 30+ individual calls.

**Query Parameters:**
- `year` (required): Year as integer (e.g., 2024)
- `month` (required): Month as integer (1-12)

**Response:**
```json
{
  "year": 2024,
  "month": 1,
  "daily_summaries": [
    {
      "date": "2024-01-01",
      "total_income": 50000.00,
      "total_expense": 25000.00,
      "transaction_count": 5,
      "income_count": 2,
      "expense_count": 3
    },
    {
      "date": "2024-01-02",
      "total_income": 30000.00,
      "total_expense": 15000.00,
      "transaction_count": 3,
      "income_count": 1,
      "expense_count": 2
    }
    // ... one entry for each day in the month
  ],
  "monthly_totals": {
    "total_income": 1500000.00,
    "total_expense": 750000.00,
    "total_transactions": 150,
    "total_income_count": 60,
    "total_expense_count": 90
  },
  "days_with_data": 25,
  "days_in_month": 31
}
```

**Implementation Notes:**
- Return empty summaries (with 0 values) for days without transactions
- Include all days from 1 to the last day of the month
- Optimize database queries to fetch all data in a single query

---

### 5. Get Date Range Summaries ⭐ **RECOMMENDED**
**New Endpoint (Alternative to Monthly)**
```
GET /summary/range?start_date={start}&end_date={end}
```

**Purpose:** More flexible endpoint that can return daily summaries for any date range.

**Query Parameters:**
- `start_date` (required): Start date in `YYYY-MM-DD` format
- `end_date` (required): End date in `YYYY-MM-DD` format

**Response:**
```json
{
  "start_date": "2024-01-01",
  "end_date": "2024-01-31",
  "daily_summaries": [
    {
      "date": "2024-01-01",
      "total_income": 50000.00,
      "total_expense": 25000.00,
      "transaction_count": 5,
      "income_count": 2,
      "expense_count": 3
    }
    // ... one entry for each day in range
  ],
  "range_totals": {
    "total_income": 1500000.00,
    "total_expense": 750000.00,
    "total_transactions": 150
  },
  "days_in_range": 31,
  "days_with_data": 25
}
```

**Implementation Notes:**
- Limit the maximum range to prevent performance issues (e.g., max 90 days)
- Return empty summaries for days without data
- Consider pagination for very large ranges

---

### 6. Get Statistics Overview ⭐ **OPTIONAL (Nice to Have)**
**New Endpoint (Optional Enhancement)**
```
GET /statistics/overview?period={period}
```

**Purpose:** Get a comprehensive statistics overview for quick dashboard display.

**Query Parameters:**
- `period` (optional): `week`, `month`, or `year` (default: `month`)

**Response:**
```json
{
  "period": "month",
  "period_start": "2024-01-01",
  "period_end": "2024-01-31",
  "totals": {
    "total_income": 1500000.00,
    "total_expense": 750000.00,
    "net_balance": 750000.00,
    "transaction_count": 150
  },
  "expenses_by_category": {
    "Food": 200000.00,
    "Transport": 150000.00,
    "Shopping": 100000.00,
    "Entertainment": 75000.00,
    "Bills": 125000.00,
    "Health": 50000.00,
    "Education": 25000.00,
    "Other": 25000.00
  },
  "income_by_category": {
    "Salary": 1000000.00,
    "Business": 300000.00,
    "Other": 200000.00
  },
  "trends": {
    "income_change_percent": 15.5,
    "expense_change_percent": -5.2,
    "compared_to_previous_period": "month"
  }
}
```

---

## Error Responses

All endpoints should return consistent error responses:

**400 Bad Request:**
```json
{
  "message": "Invalid date format",
  "errors": {
    "date": ["The date must be in YYYY-MM-DD format"]
  }
}
```

**401 Unauthorized:**
```json
{
  "message": "Unauthenticated"
}
```

**404 Not Found:**
```json
{
  "message": "Resource not found"
}
```

**422 Validation Error:**
```json
{
  "message": "The given data was invalid",
  "errors": {
    "start_date": ["The start date must be before end date"]
  }
}
```

**500 Server Error:**
```json
{
  "message": "Internal server error"
}
```

---

## Implementation Priority

### High Priority (Required for Optimal Performance)
1. ✅ **Get Monthly Summaries** (`/summary/monthly`) - This will eliminate 30+ API calls per month view
2. ✅ **Get Date Range Summaries** (`/summary/range`) - Alternative flexible solution

### Medium Priority (Performance Improvement)
3. **Get Statistics Overview** (`/statistics/overview`) - Nice to have for dashboard

### Low Priority (Already Working)
4. ✅ Get Daily Summary - Already implemented
5. ✅ Get Weekly Summaries - Already implemented  
6. ✅ Get Expenses by Category - Already implemented

---

## Current Implementation Status

The Flutter app currently works with the existing endpoints but makes multiple API calls for monthly view:
- **Week View**: 1 API call (efficient ✅)
- **Month View**: 30+ API calls (inefficient ❌) - Needs `/summary/monthly` endpoint

---

## Database Query Optimization Tips

For the monthly summary endpoint, consider using:

```sql
-- Example optimized query structure
SELECT 
  DATE(created_at) as date,
  SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
  SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense,
  COUNT(*) as transaction_count,
  SUM(CASE WHEN type = 'income' THEN 1 ELSE 0 END) as income_count,
  SUM(CASE WHEN type = 'expense' THEN 1 ELSE 0 END) as expense_count
FROM transactions
WHERE user_id = ?
  AND YEAR(created_at) = ?
  AND MONTH(created_at) = ?
  AND deleted_at IS NULL
GROUP BY DATE(created_at)
ORDER BY date ASC
```

This will fetch all daily summaries in a single query instead of 30+ individual queries.

---

## Testing

Test the endpoints with:
- Empty months (no transactions)
- Months with partial data
- Edge cases (first day, last day of month)
- Different months with varying numbers of days
- Performance with large datasets

---

## Notes

- All dates should be in `YYYY-MM-DD` format
- All monetary values should be in the smallest currency unit (e.g., cents) or as decimals
- Timezone handling: Use UTC or user's timezone consistently
- Consider caching monthly summaries for better performance
- Add rate limiting to prevent abuse

