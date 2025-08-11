#!/usr/bin/env python3
"""
IBTrACS Database Query Utility
==============================

Simple utility for querying the IBTrACS database.
"""

import mysql.connector
import pandas as pd
import argparse
import sys
from datetime import datetime

# Database configuration
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'ibtracs_db'
}

def connect_db():
    """Connect to the database."""
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        return conn
    except Exception as e:
        print(f"Error connecting to database: {e}")
        return None

def get_storm_count():
    """Get total number of storms."""
    conn = connect_db()
    if not conn:
        return None
    
    try:
        cursor = conn.cursor()
        cursor.execute("SELECT COUNT(*) FROM storms")
        count = cursor.fetchone()[0]
        return count
    except Exception as e:
        print(f"Error getting storm count: {e}")
        return None
    finally:
        conn.close()

def get_storms_by_name(name, limit=10):
    """Get storms by name."""
    conn = connect_db()
    if not conn:
        return None
    
    try:
        query = """
            SELECT s.sid, s.name, s.season, s.basin, s.latitude, s.longitude, 
                   s.wind, s.pressure, ss.category, ss.max_wind
            FROM storms s
            LEFT JOIN storm_stats ss ON s.sid = ss.sid
            WHERE s.name LIKE %s
            ORDER BY s.season DESC, s.name
            LIMIT %s
        """
        
        cursor = conn.cursor(dictionary=True)
        cursor.execute(query, (f'%{name}%', limit))
        results = cursor.fetchall()
        
        if results:
            df = pd.DataFrame(results)
            return df
        else:
            print(f"No storms found with name containing '{name}'")
            return None
            
    except Exception as e:
        print(f"Error querying storms by name: {e}")
        return None
    finally:
        conn.close()

def get_storms_by_basin(basin, limit=10):
    """Get storms by basin."""
    conn = connect_db()
    if not conn:
        return None
    
    try:
        query = """
            SELECT s.sid, s.name, s.season, s.basin, s.latitude, s.longitude, 
                   s.wind, s.pressure, ss.category, ss.max_wind
            FROM storms s
            LEFT JOIN storm_stats ss ON s.sid = ss.sid
            WHERE s.basin = %s
            ORDER BY s.season DESC, s.name
            LIMIT %s
        """
        
        cursor = conn.cursor(dictionary=True)
        cursor.execute(query, (basin, limit))
        results = cursor.fetchall()
        
        if results:
            df = pd.DataFrame(results)
            return df
        else:
            print(f"No storms found in basin '{basin}'")
            return None
            
    except Exception as e:
        print(f"Error querying storms by basin: {e}")
        return None
    finally:
        conn.close()

def get_storms_by_year(year, limit=10):
    """Get storms by year."""
    conn = connect_db()
    if not conn:
        return None
    
    try:
        query = """
            SELECT s.sid, s.name, s.season, s.basin, s.latitude, s.longitude, 
                   s.wind, s.pressure, ss.category, ss.max_wind
            FROM storms s
            LEFT JOIN storm_stats ss ON s.sid = ss.sid
            WHERE s.season = %s
            ORDER BY s.name
            LIMIT %s
        """
        
        cursor = conn.cursor(dictionary=True)
        cursor.execute(query, (year, limit))
        results = cursor.fetchall()
        
        if results:
            df = pd.DataFrame(results)
            return df
        else:
            print(f"No storms found in year {year}")
            return None
            
    except Exception as e:
        print(f"Error querying storms by year: {e}")
        return None
    finally:
        conn.close()

def get_category_5_storms(limit=10):
    """Get Category 5 storms."""
    conn = connect_db()
    if not conn:
        return None
    
    try:
        query = """
            SELECT s.sid, s.name, s.season, s.basin, s.latitude, s.longitude, 
                   s.wind, s.pressure, ss.category, ss.max_wind
            FROM storms s
            LEFT JOIN storm_stats ss ON s.sid = ss.sid
            WHERE ss.category = 'Category 5' OR s.wind >= 135
            ORDER BY s.season DESC, s.name
            LIMIT %s
        """
        
        cursor = conn.cursor(dictionary=True)
        cursor.execute(query, (limit,))
        results = cursor.fetchall()
        
        if results:
            df = pd.DataFrame(results)
            return df
        else:
            print("No Category 5 storms found")
            return None
            
    except Exception as e:
        print(f"Error querying Category 5 storms: {e}")
        return None
    finally:
        conn.close()

def get_basin_summary():
    """Get basin summary statistics."""
    conn = connect_db()
    if not conn:
        return None
    
    try:
        query = """
            SELECT 
                basin,
                COUNT(DISTINCT sid) as total_storms,
                COUNT(DISTINCT season) as total_seasons,
                MIN(season) as first_season,
                MAX(season) as last_season,
                AVG(wind) as avg_wind,
                MAX(wind) as max_wind,
                MIN(pressure) as min_pressure
            FROM storms
            WHERE basin IS NOT NULL
            GROUP BY basin
            ORDER BY total_storms DESC
        """
        
        cursor = conn.cursor(dictionary=True)
        cursor.execute(query)
        results = cursor.fetchall()
        
        if results:
            df = pd.DataFrame(results)
            return df
        else:
            print("No basin data found")
            return None
            
    except Exception as e:
        print(f"Error querying basin summary: {e}")
        return None
    finally:
        conn.close()

def get_year_summary():
    """Get year summary statistics."""
    conn = connect_db()
    if not conn:
        return None
    
    try:
        query = """
            SELECT 
                season as year,
                COUNT(DISTINCT sid) as total_storms,
                COUNT(DISTINCT basin) as basins_affected,
                AVG(wind) as avg_wind,
                MAX(wind) as max_wind,
                MIN(pressure) as min_pressure
            FROM storms
            WHERE season IS NOT NULL
            GROUP BY season
            ORDER BY season DESC
            LIMIT 20
        """
        
        cursor = conn.cursor(dictionary=True)
        cursor.execute(query)
        results = cursor.fetchall()
        
        if results:
            df = pd.DataFrame(results)
            return df
        else:
            print("No year data found")
            return None
            
    except Exception as e:
        print(f"Error querying year summary: {e}")
        return None
    finally:
        conn.close()

def custom_query(query, params=None):
    """Execute a custom SQL query."""
    conn = connect_db()
    if not conn:
        return None
    
    try:
        cursor = conn.cursor(dictionary=True)
        if params:
            cursor.execute(query, params)
        else:
            cursor.execute(query)
        
        results = cursor.fetchall()
        
        if results:
            df = pd.DataFrame(results)
            return df
        else:
            print("No results found")
            return None
            
    except Exception as e:
        print(f"Error executing custom query: {e}")
        return None
    finally:
        conn.close()

def main():
    """Main function."""
    parser = argparse.ArgumentParser(description='IBTrACS Database Query Utility')
    parser.add_argument('--host', default='localhost', help='Database host')
    parser.add_argument('--user', default='root', help='Database user')
    parser.add_argument('--password', default='', help='Database password')
    parser.add_argument('--database', default='ibtracs_db', help='Database name')
    
    subparsers = parser.add_subparsers(dest='command', help='Available commands')
    
    # Count command
    subparsers.add_parser('count', help='Get total storm count')
    
    # Name command
    name_parser = subparsers.add_parser('name', help='Search storms by name')
    name_parser.add_argument('storm_name', help='Storm name to search for')
    name_parser.add_argument('--limit', type=int, default=10, help='Limit results')
    
    # Basin command
    basin_parser = subparsers.add_parser('basin', help='Search storms by basin')
    basin_parser.add_argument('basin_name', help='Basin name (e.g., NA, EP, WP)')
    basin_parser.add_argument('--limit', type=int, default=10, help='Limit results')
    
    # Year command
    year_parser = subparsers.add_parser('year', help='Search storms by year')
    year_parser.add_argument('year', type=int, help='Year to search for')
    year_parser.add_argument('--limit', type=int, default=10, help='Limit results')
    
    # Category 5 command
    cat5_parser = subparsers.add_parser('cat5', help='Get Category 5 storms')
    cat5_parser.add_argument('--limit', type=int, default=10, help='Limit results')
    
    # Basin summary command
    subparsers.add_parser('basin-summary', help='Get basin summary statistics')
    
    # Year summary command
    subparsers.add_parser('year-summary', help='Get year summary statistics')
    
    # Custom query command
    query_parser = subparsers.add_parser('query', help='Execute custom SQL query')
    query_parser.add_argument('sql', help='SQL query to execute')
    
    args = parser.parse_args()
    
    # Update configuration
    global DB_CONFIG
    DB_CONFIG.update({
        'host': args.host,
        'user': args.user,
        'password': args.password,
        'database': args.database
    })
    
    if not args.command:
        parser.print_help()
        return
    
    # Execute commands
    if args.command == 'count':
        count = get_storm_count()
        if count is not None:
            print(f"Total storms in database: {count:,}")
    
    elif args.command == 'name':
        df = get_storms_by_name(args.storm_name, args.limit)
        if df is not None:
            print(f"\nStorms with name containing '{args.storm_name}':")
            print(df.to_string(index=False))
    
    elif args.command == 'basin':
        df = get_storms_by_basin(args.basin_name, args.limit)
        if df is not None:
            print(f"\nStorms in basin '{args.basin_name}':")
            print(df.to_string(index=False))
    
    elif args.command == 'year':
        df = get_storms_by_year(args.year, args.limit)
        if df is not None:
            print(f"\nStorms in year {args.year}:")
            print(df.to_string(index=False))
    
    elif args.command == 'cat5':
        df = get_category_5_storms(args.limit)
        if df is not None:
            print(f"\nCategory 5 storms:")
            print(df.to_string(index=False))
    
    elif args.command == 'basin-summary':
        df = get_basin_summary()
        if df is not None:
            print(f"\nBasin Summary:")
            print(df.to_string(index=False))
    
    elif args.command == 'year-summary':
        df = get_year_summary()
        if df is not None:
            print(f"\nYear Summary (last 20 years):")
            print(df.to_string(index=False))
    
    elif args.command == 'query':
        df = custom_query(args.sql)
        if df is not None:
            print(f"\nQuery Results:")
            print(df.to_string(index=False))

if __name__ == "__main__":
    main()
