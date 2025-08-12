# Blog Platform

A modern blogging platform built with Vue 3, Bootstrap 5, and Axios. Features a clean, minimal black/white design with full CRUD operations for blog posts, user authentication, and responsive design.

## 🚀 Features

- **Modern Tech Stack**: Vue 3 (Options API), Bootstrap 5, Axios
- **Responsive Design**: Mobile-first approach with Bootstrap utilities
- **Authentication**: Login/Register with form validation
- **Blog Management**: Create, read, update, delete posts
- **User Dashboard**: Statistics and post management
- **Profile Management**: User settings and preferences
- **Comments System**: Interactive commenting on posts
- **Clean UI**: Minimal black/white theme with custom CSS variables

## 📋 Prerequisites

- Node.js (v16 or higher)
- npm or yarn package manager

## 🛠️ Installation

### 1. Clone and Setup

```bash
# Navigate to the client directory
cd client

# Install dependencies
npm install
```

### 2. Development Server

```bash
# Start development server
npm run dev
```

The application will be available at `http://localhost:3000`

### 3. Build for Production

```bash
# Build the application
npm run build

# Preview the production build
npm run preview
```

## 📁 Project Structure

```
client/
├── public/                 # Static assets
├── src/
│   ├── assets/
│   │   └── scss/
│   │       └── main.scss   # Global styles and theme variables
│   ├── components/
│   │   ├── GlobalNavbar.vue    # Navigation component
│   │   └── Footer.vue          # Footer component
│   ├── views/
│   │   ├── Home.vue            # Home page with blog listing
│   │   ├── PostDetail.vue      # Individual post view
│   │   ├── Login.vue           # Authentication login
│   │   ├── Register.vue        # User registration
│   │   ├── Dashboard.vue       # User dashboard
│   │   └── Profile.vue         # User profile settings
│   ├── router/
│   │   └── index.js            # Vue Router configuration
│   ├── api.js                  # Axios instance and API endpoints
│   ├── App.vue                 # Root component
│   └── main.js                 # Application entry point
├── package.json
├── vite.config.js
└── README.md
```

## 🎨 Theme & Styling

The application uses a custom black/white minimal theme with CSS variables:

- **Primary Color**: `#000000` (Black)
- **Secondary Color**: `#333333` (Dark Gray)
- **Background**: `#ffffff` (White)
- **Surface**: `#f8f9fa` (Light Gray)
- **Borders**: `#dee2e6` (Light Gray)

All styling is done through Bootstrap utility classes and custom CSS variables defined in `src/assets/scss/main.scss`.

## 🔌 API Integration

The application is configured to work with a backend API. API endpoints are defined in `src/api.js`:

### Base Configuration
- **Base URL**: `/api` (proxied to `http://localhost:8000`)
- **Credentials**: `withCredentials: true` for session handling
- **Timeout**: 10 seconds

### Available Endpoints

#### Posts
- `GET /api/posts` - Get all posts with pagination
- `GET /api/posts/:id` - Get single post
- `POST /api/posts` - Create new post
- `PUT /api/posts/:id` - Update post
- `DELETE /api/posts/:id` - Delete post

#### Authentication
- `POST /api/auth/login` - User login
- `POST /api/auth/register` - User registration
- `POST /api/auth/logout` - User logout
- `GET /api/auth/profile` - Get user profile
- `PUT /api/auth/profile` - Update user profile

#### Comments
- `GET /api/posts/:id/comments` - Get post comments
- `POST /api/posts/:id/comments` - Create comment
- `DELETE /api/comments/:id` - Delete comment

## 🔧 Configuration

### Vite Configuration

The development server is configured with:
- **Port**: 3000
- **API Proxy**: `/api` → `http://localhost:8000`
- **Vue Plugin**: Enabled for SFC support

### Environment Variables

Create a `.env` file in the client directory for environment-specific configuration:

```env
VITE_API_BASE_URL=http://localhost:8000
VITE_APP_TITLE=Blog Platform
```

## 🚀 Deployment

### Build for Production

```bash
npm run build
```

This creates a `dist/` folder with optimized production files.

### Deploy to Static Hosting

The built application can be deployed to any static hosting service:

- **Netlify**: Drag and drop the `dist/` folder
- **Vercel**: Connect your repository and set build command
- **GitHub Pages**: Use GitHub Actions for deployment
- **AWS S3**: Upload `dist/` contents to an S3 bucket

## 🔒 Authentication

The application includes a complete authentication system:

- **Login/Register Forms**: With validation and error handling
- **Protected Routes**: Dashboard and Profile require authentication
- **Session Management**: Uses cookies for session persistence
- **Route Guards**: Automatic redirection for unauthenticated users

## 📱 Responsive Design

The application is fully responsive with:

- **Mobile-First**: Bootstrap 5 responsive utilities
- **Breakpoints**: xs, sm, md, lg, xl, xxl
- **Flexible Layout**: Cards and grids adapt to screen size
- **Touch-Friendly**: Optimized for mobile interactions

## 🎯 Key Components

### GlobalNavbar
- Responsive navigation with mobile menu
- Authentication state management
- User dropdown with profile links

### Dashboard
- User statistics overview
- Recent posts management
- Quick actions for post creation

### Profile
- Tabbed interface for different settings
- Profile information management
- Security settings (password change)
- Notification preferences

## 🐛 Development

### Adding New Features

1. **New Routes**: Add to `src/router/index.js`
2. **New Components**: Create in `src/components/`
3. **New Views**: Create in `src/views/`
4. **API Endpoints**: Add to `src/api.js`

### Styling Guidelines

- Use Bootstrap utility classes when possible
- Custom CSS only for theme variables and layout tweaks
- Follow the black/white minimal theme
- Use CSS variables for consistent theming

## 📝 TODO

- [ ] Implement backend API integration
- [ ] Add image upload functionality
- [ ] Implement search functionality
- [ ] Add pagination for comments
- [ ] Create admin panel
- [ ] Add social media sharing
- [ ] Implement email notifications
- [ ] Add dark mode toggle

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License.

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Check the documentation
- Review the code comments for implementation details

---

Built with ❤️ using Vue 3, Bootstrap 5, and modern web technologies.
