# Passmush
# PHP/MySQL Secure Password & Text Sharing Application (90% made by AI 😏)

A secure, user-friendly web application for sharing sensitive passwords and text information with automatic expiration and view limits.

## TO DO
- API access to create URLs
- Company name change at install
- Maybe e-mail sending (from a third tab)

## 🌟 Key Features

### Security
- End-to-end encryption using OpenSSL
- Automatic password expiration
- View-limit controls
- IP-based rate limiting to prevent abuse
- Self-destructing links
- No password storage - only encrypted data
- Session security measures

### User Experience
- Clean, modern interface using Chota CSS
- Mobile-responsive design
- Password blur protection
- One-click copy functionality
- Password generator
- Visual feedback for all actions

### Sharing Options
- Share passwords or text snippets
- Customizable expiration times:
  - 1 hour
  - 2 hours
  - 6 hours
  - 24 hours
  - 3 days
  - 1 week
  - 1 month
  - Unlimited (not recommended)
- Flexible view limits:
  - Single view
  - 3 views
  - 5 views
  - 10 views
  - Unlimited views

### Internationalization
- Multi-language support (English/French)
- Easy language switching
- URL parameter preservation

### Privacy & Security Features
- No data tracking
- No cookies required
- No user accounts needed
- Automatic data cleanup
- Manual destroy option
- Rate limiting protection

### Technical Features
- PDO database abstraction
- Exception handling
- Secure configuration
- Installation wizard
- Environment checks
- Custom error pages

## 🛠️ Installation

1. Upload files to your web server
2. Visit the installation page (install.php)
3. Follow the setup wizard
4. Configure database settings
5. Set up encryption keys
6. Remove install.php file

## 🔒 Security Recommendations

- Use HTTPS
- Set up proper file permissions
- Configure server security
- Regular backups
- Monitor access logs
- Update dependencies

## 💡 Best Practices

- Share links through different channels than passwords
- Use short expiration times
- Enable view limits
- Delete sensitive data immediately
- Use secure communication channels

## 🌐 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## 🤝 Contributing

Contributions are welcome! Please feel free to submit pull requests.

## 📝 License

This project is licensed under the CC License - see the LICENSE file for details.
