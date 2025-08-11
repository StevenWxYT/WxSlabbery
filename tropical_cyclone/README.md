# IBTrACS - International Best Track Archive for Climate Stewardship

A comprehensive web application for viewing, analyzing, and tracking tropical cyclone data from the IBTrACS database.

## Features

### 🌪️ **Main Features**
- **Interactive Data Browser**: Search and filter tropical cyclone data by name, year, basin, and SID
- **Real-time Statistics**: Live statistics dashboard with key metrics
- **Advanced Analytics**: Comprehensive data analysis with interactive charts
- **Storm Tracker**: Interactive map for visualizing storm tracks and paths
- **Responsive Design**: Modern, mobile-friendly interface

### 📊 **Dashboard**
- Total storms, seasons, and basins overview
- Basin distribution charts
- Annual storm frequency trends
- Recent storms list
- Real-time statistics updates

### 🗺️ **Storm Tracker**
- Interactive Leaflet map
- Storm path visualization
- Color-coded intensity tracking
- Heatmap overlay
- Storm details panel

### 📈 **Analytics**
- Annual storm frequency analysis
- Basin distribution over time
- Intensity distribution charts
- Wind speed trends
- Wind vs pressure correlation
- Statistical summaries
- Key insights generation

## Installation

### Prerequisites
- Python 3.8 or higher
- MySQL/MariaDB database
- Web browser with JavaScript enabled

### Setup Instructions

1. **Clone or download the project**
   ```bash
   cd tropical_cyclone
   ```

2. **Install Python dependencies**
   ```bash
   pip install -r requirements.txt
   ```

3. **Database Setup**
   - Create a MySQL database named `cyclone_db`
   - Update database configuration in `app.py`:
     ```python
     db_config = {
         'host': 'localhost',
         'user': 'your_username',
         'password': 'your_password',
         'database': 'cyclone_db'
     }
     ```

4. **IBTrACS Data**
   - Download IBTrACS data from [NOAA's IBTrACS website](https://www.ncei.noaa.gov/products/international-best-track-archive)
   - Place the CSV file in `ibtracs_master/data/` directory
   - Rename it to `ibtracs.ALL.list.v04r00.csv`

5. **Run the Application**
   ```bash
   python app.py
   ```

6. **Access the Application**
   - Open your web browser
   - Navigate to `http://localhost:5000`

## Usage

### Main Page (`/`)
- View overview statistics
- Search and filter storm data
- Browse storm records with pagination
- Export filtered results

### Dashboard (`/dashboard`)
- View comprehensive statistics
- Interactive charts and graphs
- Recent storms overview
- Real-time data updates

### Storm Tracker (`/storm-tracker`)
- Interactive map interface
- Search for specific storms
- View storm tracks and paths
- Toggle heatmap overlay
- Detailed storm information

### Analytics (`/analytics`)
- Advanced data analysis
- Multiple chart types
- Statistical summaries
- Key insights generation
- Filter by time periods and basins

## API Endpoints

### Data Retrieval
- `GET /api/ibtracs` - Get storm data with filters
- `GET /api/storm/<sid>` - Get specific storm details
- `GET /api/statistics` - Get overall statistics
- `GET /api/basins` - Get available basins
- `GET /api/seasons` - Get available years

### Parameters
- `name` - Storm name filter
- `season` - Year filter
- `basin` - Basin filter
- `sid` - Storm ID filter
- `limit` - Maximum results (default: 1000)

## Database Schema

### Main Tables
- `IBTrACS_Storms` - Primary storm data
- `storm_tracks` - Detailed track information
- `storm_stats` - Calculated statistics

### Key Fields
- `sid` - Unique storm identifier
- `name` - Storm name
- `season` - Year
- `basin` - Ocean basin
- `latitude/longitude` - Location coordinates
- `wind` - Wind speed (knots)
- `pressure` - Atmospheric pressure (hPa)

## Configuration

### Environment Variables
- Database connection settings
- CSV file path
- Application port and host

### Customization
- Modify CSS in `static/ibtracs.css`
- Update JavaScript in `static/ibtracs.js`
- Customize templates in `templates/` directory

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Verify MySQL is running
   - Check database credentials
   - Ensure database exists

2. **CSV Import Issues**
   - Verify CSV file path
   - Check file format and encoding
   - Ensure sufficient disk space

3. **Chart Display Problems**
   - Check JavaScript console for errors
   - Verify Chart.js is loaded
   - Ensure data is properly formatted

4. **Map Not Loading**
   - Check internet connection (for map tiles)
   - Verify Leaflet library is loaded
   - Check browser console for errors

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open source and available under the MIT License.

## Acknowledgments

- NOAA for providing IBTrACS data
- OpenStreetMap for map tiles
- Chart.js for data visualization
- Leaflet for interactive maps

## Support

For issues and questions:
- Check the troubleshooting section
- Review the API documentation
- Submit an issue on GitHub

---

**IBTrACS** - Making tropical cyclone data accessible and understandable for researchers, meteorologists, and the public. 