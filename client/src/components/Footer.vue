<template>
  <footer class="footer">
    <div class="container">
      <div class="row">
        <!-- Brand Section -->
        <div class="col-md-4 mb-4">
          <div class="brand-section">
            <h5 class="brand-title mb-3">
              <i class="bi bi-pen-fill brand-icon"></i>
              Zura Blog
            </h5>
            <p class="brand-description mb-3">
              Where stories come alive and ideas flourish. Join our community of
              passionate writers and curious readers.
            </p>
            <div class="social-links">
              <a href="#" class="social-link" title="Twitter">
                <i class="bi bi-twitter"></i>
              </a>
              <a href="#" class="social-link" title="Facebook">
                <i class="bi bi-facebook"></i>
              </a>
              <a href="#" class="social-link" title="Instagram">
                <i class="bi bi-instagram"></i>
              </a>
              <a href="#" class="social-link" title="LinkedIn">
                <i class="bi bi-linkedin"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="col-md-2 mb-4">
          <h6 class="footer-heading mb-3">Explore</h6>
          <ul class="footer-links">
            <li>
              <router-link to="/" class="footer-link">
                <i class="bi bi-house-door"></i>
                Home
              </router-link>
            </li>
            <li>
              <router-link to="/categories" class="footer-link">
                <i class="bi bi-tags"></i>
                Categories
              </router-link>
            </li>
            <li>
              <router-link to="/about" class="footer-link">
                <i class="bi bi-info-circle"></i>
                About
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Account Links -->
        <div class="col-md-2 mb-4">
          <h6 class="footer-heading mb-3">Account</h6>
          <ul class="footer-links">
            <li>
              <router-link :to="{ name: 'Login' }" class="footer-link">
                <i class="bi bi-box-arrow-in-right"></i>
                Sign In
              </router-link>
            </li>
            <li>
              <router-link :to="{ name: 'Register' }" class="footer-link">
                <i class="bi bi-person-plus"></i>
                Join Us
              </router-link>
            </li>
            <li>
              <router-link :to="{ name: 'Login' }" class="footer-link">
                <i class="bi bi-pencil-square"></i>
                Write
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Contact & Support -->
        <div class="col-md-4 mb-4">
          <h6 class="footer-heading mb-3">Stay Connected</h6>
          <div class="contact-info">
            <div class="contact-item mb-2">
              <i class="bi bi-envelope contact-icon"></i>
              <span>hello@zurablog.com</span>
            </div>
            <div class="contact-item mb-3">
              <i class="bi bi-geo-alt contact-icon"></i>
              <span>Building communities worldwide</span>
            </div>
          </div>

          <div class="newsletter-signup">
            <p class="newsletter-text mb-2">
              Get the best stories in your inbox
            </p>
            <div class="input-group">
              <input
                type="email"
                class="form-control newsletter-input"
                placeholder="Enter your email"
                v-model="email"
              />
              <button class="btn newsletter-btn" @click="subscribe">
                <i class="bi bi-arrow-right"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Section -->
      <hr class="footer-divider" />
      <div class="footer-bottom">
        <div class="row align-items-center">
          <div class="col-md-6">
            <p class="copyright mb-0">
              &copy; {{ currentYear }} Zura Blog. Crafted with passion.
            </p>
          </div>
          <div class="col-md-6">
            <div class="footer-meta">
              <span class="tech-stack">
                <i class="bi bi-code-slash"></i>
                Vue 3 • Bootstrap 5
              </span>
              <span class="love-indicator">
                Made with <i class="bi bi-heart-fill heart"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: "Footer",
  data() {
    return {
      email: "",
    };
  },
  computed: {
    currentYear() {
      return new Date().getFullYear();
    },
  },
  methods: {
    subscribe() {
      if (this.email) {
        // Handle newsletter subscription
        console.log("Subscribing email:", this.email);
        this.email = "";
        // Add your subscription logic here
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.footer {
  background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
  color: white;
  padding: 4rem 0 2rem;
  margin-top: auto;
  position: relative;
  overflow: hidden;
}

.footer::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent, #667eea, transparent);
}

/* Brand Section */
.brand-section {
  .brand-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    display: flex;
    align-items: center;
    gap: 0.5rem;

    .brand-icon {
      color: #667eea;
    }
  }

  .brand-description {
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.6;
    font-size: 0.95rem;
  }
}

.social-links {
  display: flex;
  gap: 0.75rem;

  .social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);

    &:hover {
      background: #667eea;
      color: white;
      transform: translateY(-2px);
    }
  }
}

/* Footer Sections */
.footer-heading {
  font-weight: 600;
  color: white;
  font-size: 1rem;
  margin-bottom: 1rem;
}

.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;

  li {
    margin-bottom: 0.75rem;
  }

  .footer-link {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    font-size: 0.9rem;

    i {
      width: 16px;
      opacity: 0.6;
    }

    &:hover {
      color: #667eea;
      transform: translateX(4px);

      i {
        opacity: 1;
      }
    }
  }
}

/* Contact Section */
.contact-info {
  .contact-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;

    .contact-icon {
      color: #667eea;
      width: 16px;
    }
  }
}

.newsletter-signup {
  .newsletter-text {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    margin-bottom: 0.75rem;
  }

  .input-group {
    max-width: 280px;

    .newsletter-input {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 25px 0 0 25px;
      padding: 0.6rem 1rem;

      &::placeholder {
        color: rgba(255, 255, 255, 0.5);
      }

      &:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        color: white;
      }
    }

    .newsletter-btn {
      background: #667eea;
      border: 1px solid #667eea;
      border-radius: 0 25px 25px 0;
      color: white;
      padding: 0.6rem 1rem;

      &:hover {
        background: #5a6fd8;
        border-color: #5a6fd8;
      }
    }
  }
}

/* Bottom Section */
.footer-divider {
  border: none;
  height: 1px;
  background: rgba(255, 255, 255, 0.1);
  margin: 3rem 0 2rem;
}

.footer-bottom {
  .copyright {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.9rem;
  }

  .footer-meta {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 1.5rem;
    font-size: 0.85rem;

    .tech-stack {
      color: rgba(255, 255, 255, 0.6);
      display: flex;
      align-items: center;
      gap: 0.5rem;

      i {
        color: #667eea;
      }
    }

    .love-indicator {
      color: rgba(255, 255, 255, 0.6);

      .heart {
        color: #e74c3c;
        animation: heartbeat 1.5s ease-in-out infinite;
      }
    }
  }
}

@keyframes heartbeat {
  0%,
  50%,
  100% {
    transform: scale(1);
  }
  25%,
  75% {
    transform: scale(1.1);
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .footer {
    padding: 3rem 0 1.5rem;
  }

  .social-links {
    justify-content: center;
    margin-top: 1rem;
  }

  .footer-meta {
    flex-direction: column;
    gap: 0.75rem;
    text-align: center;
    margin-top: 1rem;
  }

  .newsletter-signup .input-group {
    max-width: 100%;
  }
}
</style>
