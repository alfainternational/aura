-- إنشاء قاعدة البيانات
CREATE DATABASE aura_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE aura_db;

-- جدول المستخدمين الأساسي
CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  firstname VARCHAR(40) NULL,
  lastname VARCHAR(40) NULL,
  username VARCHAR(40) UNIQUE NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  email_verified_at TIMESTAMP NULL,
  mobile VARCHAR(20) NULL,
  mobile_verified_at TIMESTAMP NULL,
  dial_code VARCHAR(10) NULL,
  country_code VARCHAR(10) NULL,
  country_name VARCHAR(100) NULL,
  city VARCHAR(100) NULL,
  state VARCHAR(100) NULL,
  zip_code VARCHAR(20) NULL,
  address TEXT NULL,
  password VARCHAR(255) NOT NULL,
  remember_token VARCHAR(100) NULL,
  profile_image VARCHAR(255) NULL,
  referral_code VARCHAR(50) NULL,
  referred_by BIGINT UNSIGNED NULL,
  status ENUM('active', 'inactive', 'suspended', 'banned') DEFAULT 'active',
  kyc_status ENUM('unverified', 'pending', 'verified', 'rejected') DEFAULT 'unverified',
  kyc_data JSON NULL,
  two_factor_enabled BOOLEAN DEFAULT FALSE,
  two_factor_secret VARCHAR(255) NULL,
  verification_code VARCHAR(100) NULL,
  last_login_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL,
  FOREIGN KEY (referred_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- جدول المتاجر
CREATE TABLE IF NOT EXISTS stores (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  merchant_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE NOT NULL,
  description TEXT NULL,
  logo VARCHAR(255) NULL,
  cover_image VARCHAR(255) NULL,
  address TEXT NULL,
  phone VARCHAR(20) NULL,
  email VARCHAR(100) NULL,
  website VARCHAR(255) NULL,
  status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
  is_featured BOOLEAN DEFAULT FALSE,
  verified_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (merchant_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول فئات المنتجات
CREATE TABLE IF NOT EXISTS product_categories (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE NOT NULL,
  description TEXT NULL,
  parent_id BIGINT UNSIGNED NULL,
  store_id BIGINT UNSIGNED NULL,
  image VARCHAR(255) NULL,
  status ENUM('active', 'inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (parent_id) REFERENCES product_categories(id) ON DELETE SET NULL,
  FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول المنتجات
CREATE TABLE IF NOT EXISTS products (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  store_id BIGINT UNSIGNED NOT NULL,
  category_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE NOT NULL,
  sku VARCHAR(100) UNIQUE NULL,
  description TEXT NULL,
  short_description VARCHAR(500) NULL,
  price DECIMAL(20,4) NOT NULL,
  cost_price DECIMAL(20,4) NULL,
  discount_price DECIMAL(20,4) NULL,
  stock INT UNSIGNED NOT NULL DEFAULT 0,
  weight DECIMAL(10,2) NULL,
  dimensions VARCHAR(100) NULL,
  is_digital BOOLEAN DEFAULT FALSE,
  requires_shipping BOOLEAN DEFAULT TRUE,
  is_featured BOOLEAN DEFAULT FALSE,
  status ENUM('active', 'inactive', 'out_of_stock') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول صور المنتجات
CREATE TABLE IF NOT EXISTS product_images (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id BIGINT UNSIGNED NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  is_primary BOOLEAN DEFAULT FALSE,
  sort_order INT UNSIGNED DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول متغيرات المنتجات
CREATE TABLE IF NOT EXISTS product_variants (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  sku VARCHAR(100) UNIQUE NULL,
  price_adjustment DECIMAL(20,4) DEFAULT 0.0000,
  stock INT UNSIGNED NOT NULL DEFAULT 0,
  is_default BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول خصائص المنتجات
CREATE TABLE IF NOT EXISTS product_attributes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  value VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول العملات
CREATE TABLE IF NOT EXISTS currencies (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(10) UNIQUE NOT NULL,
  name VARCHAR(100) NOT NULL,
  symbol VARCHAR(10) NOT NULL,
  exchange_rate DECIMAL(20,10) NOT NULL DEFAULT 1.0000000000,
  is_crypto BOOLEAN DEFAULT FALSE,
  is_active BOOLEAN DEFAULT TRUE,
  is_default BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- جدول المحافظ
CREATE TABLE IF NOT EXISTS wallets (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  user_type ENUM('user', 'merchant', 'agent') NOT NULL,
  currency_id BIGINT UNSIGNED NOT NULL,
  balance DECIMAL(28,8) NOT NULL DEFAULT 0.00000000,
  is_primary BOOLEAN DEFAULT FALSE,
  status ENUM('active', 'frozen', 'blocked') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (currency_id) REFERENCES currencies(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- جدول المعاملات
CREATE TABLE IF NOT EXISTS transactions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  user_type ENUM('user', 'merchant', 'agent') NOT NULL,
  wallet_id BIGINT UNSIGNED NOT NULL,
  currency_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(28,8) NOT NULL,
  transaction_type ENUM('deposit', 'withdrawal', 'transfer', 'payment', 'refund', 'commission') NOT NULL,
  description TEXT NULL,
  status ENUM('pending', 'completed', 'failed', 'reversed') NOT NULL DEFAULT 'pending',
  reference_number VARCHAR(50) UNIQUE NOT NULL,
  before_balance DECIMAL(28,8) NOT NULL,
  after_balance DECIMAL(28,8) NOT NULL,
  transaction_fee DECIMAL(20,8) DEFAULT 0.00000000,
  metadata JSON NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE,
  FOREIGN KEY (currency_id) REFERENCES currencies(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- جدول الطلبات
CREATE TABLE IF NOT EXISTS orders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_number VARCHAR(50) UNIQUE NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  store_id BIGINT UNSIGNED NOT NULL,
  total_amount DECIMAL(20,4) NOT NULL,
  subtotal DECIMAL(20,4) NOT NULL,
  tax_amount DECIMAL(20,4) DEFAULT 0.0000,
  shipping_amount DECIMAL(20,4) DEFAULT 0.0000,
  discount_amount DECIMAL(20,4) DEFAULT 0.0000,
  status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending',
  payment_status ENUM('unpaid', 'paid', 'partial', 'refunded') NOT NULL DEFAULT 'unpaid',
  shipping_method VARCHAR(100) NULL,
  tracking_number VARCHAR(100) NULL,
  billing_address TEXT NOT NULL,
  shipping_address TEXT NOT NULL,
  customer_notes TEXT NULL,
  admin_notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول بنود الطلبات
CREATE TABLE IF NOT EXISTS order_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  variant_id BIGINT UNSIGNED NULL,
  quantity INT UNSIGNED NOT NULL,
  unit_price DECIMAL(20,4) NOT NULL,
  total_price DECIMAL(20,4) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL
) ENGINE=InnoDB;
-- جدول المراسلات والمحادثات
CREATE TABLE IF NOT EXISTS conversations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  type ENUM('individual', 'group') NOT NULL DEFAULT 'individual',
  title VARCHAR(255) NULL,
  creator_id BIGINT UNSIGNED NOT NULL,
  creator_type ENUM('user', 'merchant', 'agent') NOT NULL,
  last_message_id BIGINT UNSIGNED NULL,
  is_archived BOOLEAN DEFAULT FALSE,
  is_muted BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- جدول الرسائل
CREATE TABLE IF NOT EXISTS messages (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  conversation_id BIGINT UNSIGNED NOT NULL,
  sender_id BIGINT UNSIGNED NOT NULL,
  sender_type ENUM('user', 'merchant', 'agent') NOT NULL,
  message_type ENUM('text', 'image', 'video', 'audio', 'file', 'location', 'contact') NOT NULL DEFAULT 'text',
  content TEXT NULL,
  file_path VARCHAR(255) NULL,
  file_type VARCHAR(100) NULL,
  file_size INT UNSIGNED NULL,
  is_read BOOLEAN DEFAULT FALSE,
  is_delivered BOOLEAN DEFAULT FALSE,
  is_encrypted BOOLEAN DEFAULT FALSE,
  metadata JSON NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول أعضاء المجموعات
CREATE TABLE IF NOT EXISTS group_members (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  conversation_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  user_type ENUM('user', 'merchant', 'agent') NOT NULL,
  role ENUM('admin', 'member', 'owner') NOT NULL DEFAULT 'member',
  joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_group_member (conversation_id, user_id, user_type),
  FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول جلسات المكالمات
CREATE TABLE IF NOT EXISTS call_sessions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  call_id VARCHAR(100) UNIQUE NOT NULL,
  caller_id BIGINT UNSIGNED NOT NULL,
  caller_type ENUM('user', 'merchant', 'agent') NOT NULL,
  receiver_id BIGINT UNSIGNED NOT NULL,
  receiver_type ENUM('user', 'merchant', 'agent') NOT NULL,
  call_type ENUM('audio', 'video') NOT NULL,
  status ENUM('initiated', 'ringing', 'answered', 'rejected', 'ended', 'missed') NOT NULL,
  start_time TIMESTAMP NULL,
  end_time TIMESTAMP NULL,
  duration INT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (caller_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول الإشعارات
CREATE TABLE IF NOT EXISTS notifications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  user_type ENUM('user', 'merchant', 'agent', 'admin') NOT NULL,
  type ENUM('transaction', 'message', 'order', 'system', 'marketing') NOT NULL,
  title VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  is_read BOOLEAN DEFAULT FALSE,
  data JSON NULL,
  action_url VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول عمليات السحب
CREATE TABLE IF NOT EXISTS withdrawals (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  user_type ENUM('user', 'merchant', 'agent') NOT NULL,
  wallet_id BIGINT UNSIGNED NOT NULL,
  currency_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(28,8) NOT NULL,
  fee DECIMAL(20,8) NOT NULL DEFAULT 0.00000000,
  net_amount DECIMAL(28,8) NOT NULL,
  withdrawal_method ENUM('bank_transfer', 'cash', 'mobile_money') NOT NULL,
  status ENUM('pending', 'processing', 'completed', 'failed', 'cancelled') NOT NULL DEFAULT 'pending',
  bank_details JSON NULL,
  reference_number VARCHAR(50) UNIQUE NOT NULL,
  processed_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE,
  FOREIGN KEY (currency_id) REFERENCES currencies(id) ON DELETE RESTRICT,
  FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- جدول عمليات الإيداع
CREATE TABLE IF NOT EXISTS deposits (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  user_type ENUM('user', 'merchant', 'agent') NOT NULL,
  wallet_id BIGINT UNSIGNED NOT NULL,
  currency_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(28,8) NOT NULL,
  fee DECIMAL(20,8) NOT NULL DEFAULT 0.00000000,
  net_amount DECIMAL(28,8) NOT NULL,
  deposit_method ENUM('bank_transfer', 'cash', 'mobile_money', 'online_payment') NOT NULL,
  status ENUM('pending', 'processing', 'completed', 'failed', 'cancelled') NOT NULL DEFAULT 'pending',
  reference_number VARCHAR(50) UNIQUE NOT NULL,
  transaction_details JSON NULL,
  processed_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE,
  FOREIGN KEY (currency_id) REFERENCES currencies(id) ON DELETE RESTRICT,
  FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- جدول الوكلاء
CREATE TABLE IF NOT EXISTS agents (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL UNIQUE,
  agent_code VARCHAR(50) UNIQUE NOT NULL,
  commission_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  total_transactions BIGINT UNSIGNED NOT NULL DEFAULT 0,
  total_commission DECIMAL(20,4) NOT NULL DEFAULT 0.0000,
  verification_status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  verification_documents JSON NULL,
  is_active BOOLEAN DEFAULT TRUE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول فروع الوكلاء
CREATE TABLE IF NOT EXISTS agent_branches (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  agent_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  address TEXT NOT NULL,
  city VARCHAR(100) NOT NULL,
  state VARCHAR(100) NOT NULL,
  latitude DECIMAL(10,8) NULL,
  longitude DECIMAL(11,8) NULL,
  contact_number VARCHAR(20) NOT NULL,
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (agent_id) REFERENCES agents(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول المشرفين
CREATE TABLE IF NOT EXISTS admins (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role_id BIGINT UNSIGNED NOT NULL,
  status ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
  last_login_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- جدول الأدوار والصلاحيات
CREATE TABLE IF NOT EXISTS roles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT NULL,
  is_default BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- جدول صلاحيات الأدوار
CREATE TABLE IF NOT EXISTS role_permissions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role_id BIGINT UNSIGNED NOT NULL,
  permission VARCHAR(100) NOT NULL,
  FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB;
-- جدول طلبات التحويل بين المستخدمين
CREATE TABLE IF NOT EXISTS money_requests (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sender_id BIGINT UNSIGNED NOT NULL,
  sender_type ENUM('user', 'merchant', 'agent') NOT NULL,
  receiver_id BIGINT UNSIGNED NOT NULL,
  receiver_type ENUM('user', 'merchant', 'agent') NOT NULL,
  wallet_id BIGINT UNSIGNED NOT NULL,
  currency_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(28,8) NOT NULL,
  status ENUM('pending', 'accepted', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
  note TEXT NULL,
  reference_number VARCHAR(50) UNIQUE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE,
  FOREIGN KEY (currency_id) REFERENCES currencies(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- جدول القسائم
CREATE TABLE IF NOT EXISTS vouchers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) UNIQUE NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  user_type ENUM('user', 'merchant', 'agent') NOT NULL,
  amount DECIMAL(28,8) NOT NULL,
  currency_id BIGINT UNSIGNED NOT NULL,
  status ENUM('active', 'used', 'expired') NOT NULL DEFAULT 'active',
  valid_from TIMESTAMP NULL,
  valid_until TIMESTAMP NULL,
  redeemed_by BIGINT UNSIGNED NULL,
  redeemed_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (currency_id) REFERENCES currencies(id) ON DELETE RESTRICT,
  FOREIGN KEY (redeemed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- جدول التوصيل والشحن
CREATE TABLE IF NOT EXISTS shipments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  tracking_number VARCHAR(100) UNIQUE NOT NULL,
  carrier VARCHAR(100) NOT NULL,
  status ENUM('pending', 'picked_up', 'in_transit', 'delivered', 'cancelled', 'returned') NOT NULL DEFAULT 'pending',
  sender_id BIGINT UNSIGNED NOT NULL,
  sender_type ENUM('user', 'merchant') NOT NULL,
  recipient_id BIGINT UNSIGNED NOT NULL,
  recipient_type ENUM('user', 'merchant') NOT NULL,
  shipping_method ENUM('standard', 'express', 'overnight') NOT NULL DEFAULT 'standard',
  estimated_delivery_date DATE NULL,
  actual_delivery_date DATE NULL,
  shipping_cost DECIMAL(20,4) NOT NULL DEFAULT 0.0000,
  weight DECIMAL(10,2) NULL,
  dimensions VARCHAR(100) NULL,
  shipping_address TEXT NOT NULL,
  delivery_notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول حالات الشحن
CREATE TABLE IF NOT EXISTS shipment_statuses (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  shipment_id BIGINT UNSIGNED NOT NULL,
  status ENUM('pending', 'picked_up', 'in_transit', 'delivered', 'cancelled', 'returned') NOT NULL,
  location VARCHAR(255) NULL,
  latitude DECIMAL(10,8) NULL,
  longitude DECIMAL(11,8) NULL,
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (shipment_id) REFERENCES shipments(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول السائقين
CREATE TABLE IF NOT EXISTS drivers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL UNIQUE,
  driver_license_number VARCHAR(50) UNIQUE NOT NULL,
  vehicle_type ENUM('motorcycle', 'car', 'truck') NOT NULL,
  vehicle_plate_number VARCHAR(50) NOT NULL,
  current_latitude DECIMAL(10,8) NULL,
  current_longitude DECIMAL(11,8) NULL,
  status ENUM('available', 'busy', 'offline') NOT NULL DEFAULT 'offline',
  rating DECIMAL(3,2) NOT NULL DEFAULT 0.00,
  total_deliveries INT UNSIGNED NOT NULL DEFAULT 0,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول معاملات المحفظة غير المتصلة
CREATE TABLE IF NOT EXISTS offline_transactions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  user_type ENUM('user', 'merchant', 'agent') NOT NULL,
  transaction_type ENUM('transfer', 'payment', 'voucher_redemption') NOT NULL,
  currency_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(28,8) NOT NULL,
  receiver_id BIGINT UNSIGNED NULL,
  receiver_type ENUM('user', 'merchant', 'agent') NULL,
  reference_number VARCHAR(50) UNIQUE NOT NULL,
  details JSON NOT NULL,
  sync_status ENUM('pending', 'synced', 'failed') NOT NULL DEFAULT 'pending',
  synced_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (currency_id) REFERENCES currencies(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- جدول سجلات التسعير والرسوم
CREATE TABLE IF NOT EXISTS transaction_charges (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  transaction_type ENUM('transfer', 'deposit', 'withdrawal', 'payment') NOT NULL,
  fixed_charge DECIMAL(20,8) NOT NULL DEFAULT 0.00000000,
  percent_charge DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  minimum_charge DECIMAL(20,8) NOT NULL DEFAULT 0.00000000,
  maximum_charge DECIMAL(20,8) NOT NULL DEFAULT 0.00000000,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- جدول رموز QR
CREATE TABLE IF NOT EXISTS qr_codes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  user_type ENUM('user', 'merchant', 'agent') NOT NULL,
  unique_code VARCHAR(100) UNIQUE NOT NULL,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول المنشورات الاجتماعية
CREATE TABLE IF NOT EXISTS social_posts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  user_type ENUM('user', 'merchant', 'agent') NOT NULL,
  content TEXT NOT NULL,
  post_type ENUM('text', 'image', 'video', 'link') NOT NULL DEFAULT 'text',
  media_path VARCHAR(255) NULL,
  privacy_level ENUM('public', 'friends', 'private') NOT NULL DEFAULT 'public',
  likes_count INT UNSIGNED NOT NULL DEFAULT 0,
  comments_count INT UNSIGNED NOT NULL DEFAULT 0,
  shares_count INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول التعليقات
CREATE TABLE IF NOT EXISTS social_comments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  post_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  user_type ENUM('user', 'merchant', 'agent') NOT NULL,
  parent_comment_id BIGINT UNSIGNED NULL,
  content TEXT NOT NULL,
  likes_count INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES social_posts(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (parent_comment_id) REFERENCES social_comments(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- جدول الصداقات والعلاقات
CREATE TABLE IF NOT EXISTS social_connections (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  connected_user_id BIGINT UNSIGNED NOT NULL,
  status ENUM('pending', 'accepted', 'blocked') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY unique_connection (user_id, connected_user_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (connected_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول نظام التوصيات الذكية
CREATE TABLE IF NOT EXISTS ai_recommendations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  recommendation_type ENUM('product', 'user', 'content', 'service') NOT NULL,
  recommended_id BIGINT UNSIGNED NOT NULL,
  recommendation_score DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  reason TEXT NULL,
  is_viewed BOOLEAN DEFAULT FALSE,
  expires_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول تحليلات المستخدم
CREATE TABLE IF NOT EXISTS user_analytics (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  total_transactions INT UNSIGNED NOT NULL DEFAULT 0,
  total_transaction_amount DECIMAL(28,8) NOT NULL DEFAULT 0.00000000,
  average_transaction_value DECIMAL(28,8) NOT NULL DEFAULT 0.00000000,
  most_used_currency_id BIGINT UNSIGNED NULL,
  preferred_transaction_type ENUM('transfer', 'payment', 'deposit', 'withdrawal') NULL,
  last_activity_at TIMESTAMP NULL,
  engagement_score DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  risk_score DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (most_used_currency_id) REFERENCES currencies(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- جدول سجلات الذكاء الاصطناعي
CREATE TABLE IF NOT EXISTS ai_interaction_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  interaction_type ENUM('chatbot', 'recommendation', 'fraud_detection', 'personalization') NOT NULL,
  input_data TEXT NOT NULL,
  output_data TEXT NOT NULL,
  confidence_score DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  response_time DECIMAL(10,4) NOT NULL,
  is_successful BOOLEAN NOT NULL DEFAULT TRUE,
  error_message TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول تتبع أداء النظام
CREATE TABLE IF NOT EXISTS system_performance_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  service_name VARCHAR(100) NOT NULL,
  operation_type VARCHAR(100) NOT NULL,
  response_time DECIMAL(10,4) NOT NULL,
  status ENUM('success', 'failure', 'partial') NOT NULL,
  error_message TEXT NULL,
  request_details JSON NULL,
  server_location VARCHAR(100) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_service_performance (service_name, status)
) ENGINE=InnoDB;

-- جدول إحصائيات المعاملات اليومية
CREATE TABLE IF NOT EXISTS daily_transaction_stats (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  date DATE NOT NULL,
  total_transactions INT UNSIGNED NOT NULL DEFAULT 0,
  total_transaction_amount DECIMAL(28,8) NOT NULL DEFAULT 0.00000000,
  successful_transactions INT UNSIGNED NOT NULL DEFAULT 0,
  failed_transactions INT UNSIGNED NOT NULL DEFAULT 0,
  unique_users INT UNSIGNED NOT NULL DEFAULT 0,
  currency_id BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_daily_stats (date, currency_id),
  FOREIGN KEY (currency_id) REFERENCES currencies(id) ON DELETE CASCADE
) ENGINE=InnoDB;
-- جدول نظام الولاء والمكافآت
CREATE TABLE IF NOT EXISTS loyalty_programs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT NULL,
  start_date DATE NOT NULL,
  end_date DATE NULL,
  type ENUM('points', 'tier', 'cashback') NOT NULL,
  status ENUM('active', 'inactive', 'expired') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- جدول نقاط الولاء للمستخدمين
CREATE TABLE IF NOT EXISTS user_loyalty_points (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  loyalty_program_id BIGINT UNSIGNED NOT NULL,
  total_points DECIMAL(20,4) NOT NULL DEFAULT 0.0000,
  tier ENUM('bronze', 'silver', 'gold', 'platinum') NOT NULL DEFAULT 'bronze',
  points_earned DECIMAL(20,4) NOT NULL DEFAULT 0.0000,
  points_redeemed DECIMAL(20,4) NOT NULL DEFAULT 0.0000,
  last_earned_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (loyalty_program_id) REFERENCES loyalty_programs(id) ON DELETE CASCADE,
  UNIQUE KEY unique_user_program (user_id, loyalty_program_id)
) ENGINE=InnoDB;

-- جدول سجل معاملات نقاط الولاء
CREATE TABLE IF NOT EXISTS loyalty_point_transactions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  loyalty_program_id BIGINT UNSIGNED NOT NULL,
  transaction_type ENUM('earn', 'redeem', 'expire', 'adjust') NOT NULL,
  points DECIMAL(20,4) NOT NULL,
  reference_id BIGINT UNSIGNED NULL,
  reference_type VARCHAR(50) NULL,
  description TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (loyalty_program_id) REFERENCES loyalty_programs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول التكامل مع الخدمات الخارجية
CREATE TABLE IF NOT EXISTS external_service_integrations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  service_name VARCHAR(255) NOT NULL,
  service_type ENUM('payment', 'shipping', 'communication', 'analytics') NOT NULL,
  api_endpoint VARCHAR(255) NOT NULL,
  authentication_type ENUM('api_key', 'oauth', 'jwt', 'basic') NOT NULL,
  credentials JSON NOT NULL,
  is_active BOOLEAN DEFAULT TRUE,
  last_sync_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY unique_service_name (service_name)
) ENGINE=InnoDB;

-- جدول سجل التكامل مع الخدمات الخارجية
CREATE TABLE IF NOT EXISTS external_service_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  integration_id BIGINT UNSIGNED NOT NULL,
  operation_type VARCHAR(100) NOT NULL,
  request_payload JSON NULL,
  response_payload JSON NULL,
  status ENUM('success', 'partial', 'failed') NOT NULL,
  error_message TEXT NULL,
  response_time DECIMAL(10,4) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (integration_id) REFERENCES external_service_integrations(id) ON DELETE CASCADE,
  INDEX idx_operation_status (operation_type, status)
) ENGINE=InnoDB;

-- جدول التقارير والتحليلات المتقدمة
CREATE TABLE IF NOT EXISTS advanced_reports (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  report_name VARCHAR(255) NOT NULL,
  report_type ENUM('financial', 'user', 'transaction', 'performance', 'custom') NOT NULL,
  user_id BIGINT UNSIGNED NULL,
  parameters JSON NULL,
  generated_file_path VARCHAR(255) NULL,
  status ENUM('pending', 'generated', 'failed') NOT NULL DEFAULT 'pending',
  start_date DATE NULL,
  end_date DATE NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- جدول نظم الذكاء الاصطناعي المتقدمة
CREATE TABLE IF NOT EXISTS ai_models (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  model_name VARCHAR(255) NOT NULL,
  model_type ENUM('recommendation', 'fraud_detection', 'predictive_analysis', 'natural_language', 'image_recognition') NOT NULL,
  version VARCHAR(50) NOT NULL,
  training_data_size BIGINT UNSIGNED NOT NULL,
  accuracy_rate DECIMAL(5,2) NOT NULL,
  last_trained_at TIMESTAMP NULL,
  is_active BOOLEAN DEFAULT TRUE,
  configuration JSON NOT NULL,
  input_schema JSON NOT NULL,
  output_schema JSON NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- جدول تدريب نماذج الذكاء الاصطناعي
CREATE TABLE IF NOT EXISTS ai_model_training_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  model_id BIGINT UNSIGNED NOT NULL,
  training_start_time TIMESTAMP NOT NULL,
  training_end_time TIMESTAMP NULL,
  training_duration INT UNSIGNED NOT NULL,
  dataset_used VARCHAR(255) NOT NULL,
  training_metrics JSON NOT NULL,
  performance_improvement DECIMAL(5,2) NULL,
  hardware_used VARCHAR(100) NULL,
  status ENUM('started', 'completed', 'failed', 'interrupted') NOT NULL,
  error_log TEXT NULL,
  FOREIGN KEY (model_id) REFERENCES ai_models(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول أنظمة التوصيات الذكية المتقدمة
CREATE TABLE IF NOT EXISTS advanced_recommendation_rules (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  rule_name VARCHAR(255) NOT NULL,
  rule_type ENUM('product', 'user', 'content', 'service') NOT NULL,
  priority TINYINT UNSIGNED NOT NULL DEFAULT 1,
  conditions JSON NOT NULL,
  action_type ENUM('suggest', 'promote', 'personalize') NOT NULL,
  is_active BOOLEAN DEFAULT TRUE,
  applicable_user_segments JSON NULL,
  weight DECIMAL(5,2) NOT NULL DEFAULT 1.00,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- جدول التكامل المتقدم مع الأنظمة الخارجية
CREATE TABLE IF NOT EXISTS advanced_system_integrations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  system_name VARCHAR(255) NOT NULL,
  integration_type ENUM('payment', 'logistics', 'communication', 'banking', 'government') NOT NULL,
  connection_method ENUM('rest_api', 'soap', 'graphql', 'websocket', 'message_queue') NOT NULL,
  authentication_method ENUM('oauth2', 'jwt', 'api_key', 'certificate', 'saml') NOT NULL,
  endpoint_url VARCHAR(255) NOT NULL,
  connection_status ENUM('active', 'inactive', 'error', 'maintenance') NOT NULL DEFAULT 'inactive',
  last_successful_sync TIMESTAMP NULL,
  retry_interval INT UNSIGNED NOT NULL DEFAULT 3600,
  max_retry_attempts TINYINT UNSIGNED NOT NULL DEFAULT 3,
  security_protocol JSON NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- جدول سجلات التكامل المتقدمة
CREATE TABLE IF NOT EXISTS advanced_integration_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  integration_id BIGINT UNSIGNED NOT NULL,
  operation_type VARCHAR(100) NOT NULL,
  request_payload LONGTEXT NULL,
  response_payload LONGTEXT NULL,
  request_headers JSON NULL,
  response_headers JSON NULL,
  status ENUM('success', 'partial', 'failed', 'pending') NOT NULL,
  error_code VARCHAR(50) NULL,
  error_message TEXT NULL,
  request_time TIMESTAMP NOT NULL,
  response_time TIMESTAMP NULL,
  processing_duration DECIMAL(10,4) NULL,
  FOREIGN KEY (integration_id) REFERENCES advanced_system_integrations(id) ON DELETE CASCADE,
  INDEX idx_operation_status (operation_type, status)
) ENGINE=InnoDB;

-- جدول نظام المخاطر والتحليل المالي المتقدم
CREATE TABLE IF NOT EXISTS advanced_risk_analysis (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  risk_score DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  risk_factors JSON NOT NULL,
  suspicious_activity_count INT UNSIGNED NOT NULL DEFAULT 0,
  last_suspicious_activity_at TIMESTAMP NULL,
  transaction_pattern_deviation DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  geographical_inconsistency BOOLEAN NOT NULL DEFAULT FALSE,
  device_anomaly_score DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  recommended_actions JSON NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول التنبؤات والتحليلات المستقبلية
CREATE TABLE IF NOT EXISTS predictive_analytics (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  model_type ENUM('user_churn', 'transaction_forecast', 'market_trend', 'customer_lifetime_value') NOT NULL,
  prediction_date DATE NOT NULL,
  data_points JSON NOT NULL,
  confidence_level DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  prediction_results JSON NOT NULL,
  status ENUM('generated', 'processing', 'error') NOT NULL DEFAULT 'processing',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_prediction_type (model_type)
) ENGINE=InnoDB;
-- جدول سجلات المراجعة والتدقيق
CREATE TABLE IF NOT EXISTS audit_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  user_type ENUM('user', 'merchant', 'agent', 'admin') NOT NULL,
  action_type VARCHAR(100) NOT NULL,
  resource_type VARCHAR(100) NOT NULL,
  resource_id BIGINT UNSIGNED NULL,
  ip_address VARCHAR(45) NOT NULL,
  user_agent TEXT NULL,
  action_details JSON NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_audit_action (action_type, resource_type)
) ENGINE=InnoDB;

-- جدول الوثائق القانونية والتنظيمية
CREATE TABLE IF NOT EXISTS legal_compliance_documents (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  document_type ENUM('kyc', 'aml', 'privacy_policy', 'terms_of_service', 'user_agreement') NOT NULL,
  version VARCHAR(50) NOT NULL,
  document_path VARCHAR(255) NOT NULL,
  description TEXT NULL,
  is_active BOOLEAN DEFAULT TRUE,
  effective_date DATE NOT NULL,
  expiration_date DATE NULL,
  created_by BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_document_version (document_type, version)
) ENGINE=InnoDB;

-- جدول موافقات المستخدمين على الوثائق القانونية
CREATE TABLE IF NOT EXISTS user_legal_agreements (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  document_id BIGINT UNSIGNED NOT NULL,
  agreed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ip_address VARCHAR(45) NOT NULL,
  user_agent TEXT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (document_id) REFERENCES legal_compliance_documents(id) ON DELETE CASCADE,
  UNIQUE KEY unique_user_document_agreement (user_id, document_id)
) ENGINE=InnoDB;

-- جدول إدارة الإصدارات والتحديثات
CREATE TABLE IF NOT EXISTS system_versions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  component_name VARCHAR(100) NOT NULL,
  version_number VARCHAR(50) NOT NULL,
  release_date DATE NOT NULL,
  release_type ENUM('major', 'minor', 'patch', 'hotfix') NOT NULL,
  changelog TEXT NULL,
  deployed_by BIGINT UNSIGNED NOT NULL,
  deployment_status ENUM('pending', 'in_progress', 'completed', 'failed') NOT NULL DEFAULT 'pending',
  deployment_notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (deployed_by) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_component_version (component_name, version_number)
) ENGINE=InnoDB;

-- جدول متابعة الامتثال التنظيمي
CREATE TABLE IF NOT EXISTS regulatory_compliance_tracking (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  regulation_name VARCHAR(255) NOT NULL,
  compliance_status ENUM('compliant', 'non_compliant', 'under_review') NOT NULL DEFAULT 'under_review',
  last_audit_date DATE NULL,
  next_audit_date DATE NULL,
  compliance_details JSON NOT NULL,
  risk_assessment_score DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  remediation_required BOOLEAN NOT NULL DEFAULT FALSE,
  remediation_plan TEXT NULL,
  assigned_to BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_compliance_status (regulation_name, compliance_status)
) ENGINE=InnoDB;

-- جدول سجلات الأمن والاختراقات المحتملة
CREATE TABLE IF NOT EXISTS security_incident_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  incident_type ENUM('login_attempt', 'data_breach', 'suspicious_activity', 'unauthorized_access') NOT NULL,
  severity ENUM('low', 'medium', 'high', 'critical') NOT NULL,
  user_id BIGINT UNSIGNED NULL,
  ip_address VARCHAR(45) NOT NULL,
  user_agent TEXT NULL,
  location_data JSON NULL,
  incident_details JSON NOT NULL,
  mitigation_status ENUM('detected', 'investigated', 'resolved', 'unresolved') NOT NULL DEFAULT 'detected',
  resolution_notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  resolved_at TIMESTAMP NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_incident_type_severity (incident_type, severity)
) ENGINE=InnoDB;

-- جدول المناطق الجغرافية
CREATE TABLE IF NOT EXISTS geographic_regions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  country_code VARCHAR(10) NOT NULL,
  country_name VARCHAR(100) NOT NULL,
  state_province VARCHAR(100) NULL,
  city VARCHAR(100) NULL,
  postal_code VARCHAR(20) NULL,
  latitude DECIMAL(10,8) NULL,
  longitude DECIMAL(11,8) NULL,
  timezone VARCHAR(50) NULL,
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY unique_location (country_code, state_province, city, postal_code)
) ENGINE=InnoDB;

-- جدول المركبات المتقدم
CREATE TABLE IF NOT EXISTS advanced_vehicles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  driver_id BIGINT UNSIGNED NOT NULL,
  vehicle_type ENUM('motorcycle', 'car', 'van', 'truck', 'bicycle') NOT NULL,
  vehicle_model VARCHAR(100) NOT NULL,
  plate_number VARCHAR(50) UNIQUE NOT NULL,
  registration_number VARCHAR(50) UNIQUE NOT NULL,
  insurance_details JSON NOT NULL,
  capacity DECIMAL(10,2) NULL COMMENT 'في حالة الشحن: الوزن أو الحجم',
  fuel_type ENUM('petrol', 'diesel', 'electric', 'hybrid') NULL,
  current_status ENUM('available', 'in_use', 'maintenance', 'unavailable') NOT NULL DEFAULT 'available',
  last_maintenance_date DATE NULL,
  next_maintenance_date DATE NULL,
  tracking_device_id VARCHAR(100) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول متابعة الشحنات المتقدم
CREATE TABLE IF NOT EXISTS advanced_shipment_tracking (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  shipment_id BIGINT UNSIGNED NOT NULL,
  current_location VARCHAR(255) NOT NULL,
  location_latitude DECIMAL(10,8) NOT NULL,
  location_longitude DECIMAL(11,8) NOT NULL,
  status ENUM('pickup', 'in_transit', 'sorting', 'out_for_delivery', 'delivered', 'exception') NOT NULL,
  estimated_arrival TIMESTAMP NULL,
  actual_arrival TIMESTAMP NULL,
  temperature DECIMAL(5,2) NULL COMMENT 'للشحنات الحساسة',
  humidity DECIMAL(5,2) NULL,
  device_temperature DECIMAL(5,2) NULL,
  battery_level DECIMAL(5,2) NULL,
  tracking_device_status ENUM('active', 'low_battery', 'offline') NOT NULL DEFAULT 'active',
  additional_notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (shipment_id) REFERENCES shipments(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول تكاليف التوصيل المتقدم
CREATE TABLE IF NOT EXISTS delivery_pricing_rules (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  region_id BIGINT UNSIGNED NOT NULL,
  vehicle_type ENUM('motorcycle', 'car', 'van', 'truck', 'bicycle') NOT NULL,
  base_price DECIMAL(10,2) NOT NULL,
  price_per_km DECIMAL(10,2) NOT NULL,
  weight_pricing_factor DECIMAL(5,2) NOT NULL DEFAULT 1.00,
  distance_pricing_factor DECIMAL(5,2) NOT NULL DEFAULT 1.00,
  peak_hour_surcharge DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  minimum_charge DECIMAL(10,2) NOT NULL,
  maximum_charge DECIMAL(10,2) NULL,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  valid_from TIMESTAMP NOT NULL,
  valid_until TIMESTAMP NULL,
  FOREIGN KEY (region_id) REFERENCES geographic_regions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول متابعة أداء السائقين
CREATE TABLE IF NOT EXISTS driver_performance_metrics (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  driver_id BIGINT UNSIGNED NOT NULL,
  total_deliveries INT UNSIGNED NOT NULL DEFAULT 0,
  successful_deliveries INT UNSIGNED NOT NULL DEFAULT 0,
  late_deliveries INT UNSIGNED NOT NULL DEFAULT 0,
  average_delivery_time DECIMAL(10,2) NULL,
  customer_rating DECIMAL(3,2) NOT NULL DEFAULT 0.00,
  cancellation_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  total_distance_traveled DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  performance_score DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  last_evaluated_at TIMESTAMP NULL,
  FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول نظام التذاكر والدعم الفني
CREATE TABLE IF NOT EXISTS support_tickets (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  user_type ENUM('user', 'merchant', 'agent') NOT NULL,
  ticket_number VARCHAR(50) UNIQUE NOT NULL,
  category ENUM('technical', 'billing', 'account', 'general', 'complaint') NOT NULL,
  priority ENUM('low', 'medium', 'high', 'critical') NOT NULL DEFAULT 'medium',
  status ENUM('open', 'in_progress', 'resolved', 'closed', 'escalated') NOT NULL DEFAULT 'open',
  subject VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  assigned_agent_id BIGINT UNSIGNED NULL,
  resolution_details TEXT NULL,
  resolution_time INT UNSIGNED NULL,
  satisfaction_rating DECIMAL(3,2) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  resolved_at TIMESTAMP NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (assigned_agent_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- جدول رسائل التذاكر
CREATE TABLE IF NOT EXISTS support_ticket_messages (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ticket_id BIGINT UNSIGNED NOT NULL,
  sender_id BIGINT UNSIGNED NOT NULL,
  sender_type ENUM('user', 'support_agent', 'system') NOT NULL,
  message TEXT NOT NULL,
  attachment_path VARCHAR(255) NULL,
  is_internal_note BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ticket_id) REFERENCES support_tickets(id) ON DELETE CASCADE,
  FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول قاعدة معرفة المساعدة الذكية
CREATE TABLE IF NOT EXISTS knowledge_base_articles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE NOT NULL,
  content LONGTEXT NOT NULL,
  category ENUM('account', 'payments', 'transactions', 'security', 'general') NOT NULL,
  tags JSON NULL,
  language VARCHAR(10) NOT NULL DEFAULT 'ar',
  views_count INT UNSIGNED NOT NULL DEFAULT 0,
  helpful_count INT UNSIGNED NOT NULL DEFAULT 0,
  not_helpful_count INT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft',
  author_id BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول المساعد الذكي والروبوت
CREATE TABLE IF NOT EXISTS ai_chatbot_interactions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  session_id VARCHAR(100) NOT NULL,
  interaction_type ENUM('text', 'voice', 'image') NOT NULL,
  user_query TEXT NOT NULL,
  ai_response TEXT NOT NULL,
  confidence_score DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  resolution_status ENUM('resolved', 'partial', 'unresolved', 'escalated') NOT NULL DEFAULT 'unresolved',
  intent_detected VARCHAR(100) NULL,
  language_detected VARCHAR(10) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_interaction_status (resolution_status)
) ENGINE=InnoDB;

-- جدول إحصائيات الدعم الفني
CREATE TABLE IF NOT EXISTS support_analytics (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  date DATE NOT NULL,
  total_tickets INT UNSIGNED NOT NULL DEFAULT 0,
  open_tickets INT UNSIGNED NOT NULL DEFAULT 0,
  resolved_tickets INT UNSIGNED NOT NULL DEFAULT 0,
  average_resolution_time DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  customer_satisfaction_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  most_common_category VARCHAR(50) NULL,
  escalation_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_daily_support_stats (date)
) ENGINE=InnoDB;

-- جدول الفواتير المتقدم
CREATE TABLE IF NOT EXISTS advanced_invoices (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  user_type ENUM('user', 'merchant', 'agent') NOT NULL,
  invoice_number VARCHAR(50) UNIQUE NOT NULL,
  total_amount DECIMAL(20,4) NOT NULL,
  tax_amount DECIMAL(20,4) NOT NULL DEFAULT 0.0000,
  discount_amount DECIMAL(20,4) NOT NULL DEFAULT 0.0000,
  status ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') NOT NULL DEFAULT 'draft',
  due_date DATE NOT NULL,
  payment_method VARCHAR(50) NULL,
  notes TEXT NULL,
  related_order_id BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول بنود الفواتير
CREATE TABLE IF NOT EXISTS invoice_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  invoice_id BIGINT UNSIGNED NOT NULL,
  description VARCHAR(255) NOT NULL,
  quantity DECIMAL(10,2) NOT NULL,
  unit_price DECIMAL(20,4) NOT NULL,
  total_price DECIMAL(20,4) NOT NULL,
  tax_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  FOREIGN KEY (invoice_id) REFERENCES advanced_invoices(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول الاشتراكات والخدمات
CREATE TABLE IF NOT EXISTS subscription_plans (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT NULL,
  type ENUM('user', 'merchant', 'agent') NOT NULL,
  billing_cycle ENUM('monthly', 'quarterly', 'annually') NOT NULL,
  price DECIMAL(20,4) NOT NULL,
  features JSON NOT NULL,
  status ENUM('active', 'inactive', 'deprecated') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- جدول اشتراكات المستخدمين
CREATE TABLE IF NOT EXISTS user_subscriptions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  plan_id BIGINT UNSIGNED NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  status ENUM('active', 'cancelled', 'expired', 'suspended') NOT NULL DEFAULT 'active',
  auto_renew BOOLEAN NOT NULL DEFAULT TRUE,
  last_payment_date DATE NULL,
  next_billing_date DATE NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (plan_id) REFERENCES subscription_plans(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- جدول العروض الترويجية
CREATE TABLE IF NOT EXISTS marketing_campaigns (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT NULL,
  type ENUM('discount', 'cashback', 'referral', 'welcome_bonus') NOT NULL,
  start_date DATETIME NOT NULL,
  end_date DATETIME NOT NULL,
  discount_type ENUM('percentage', 'fixed_amount') NOT NULL,
  discount_value DECIMAL(10,2) NOT NULL,
  minimum_spend DECIMAL(20,4) NULL,
  maximum_discount DECIMAL(20,4) NULL,
  usage_limit INT UNSIGNED NULL,
  current_usage_count INT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('draft', 'active', 'expired', 'cancelled') NOT NULL DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- جدول استخدام العروض الترويجية
CREATE TABLE IF NOT EXISTS campaign_usage_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  campaign_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  transaction_id BIGINT UNSIGNED NULL,
  discount_amount DECIMAL(20,4) NOT NULL,
  usage_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (campaign_id) REFERENCES marketing_campaigns(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- جدول نظام الإحالة والعمولات
CREATE TABLE IF NOT EXISTS referral_system (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  referrer_id BIGINT UNSIGNED NOT NULL,
  referred_id BIGINT UNSIGNED NOT NULL,
  referral_code VARCHAR(50) NOT NULL,
  bonus_amount DECIMAL(20,4) NOT NULL DEFAULT 0.0000,
  status ENUM('pending', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (referrer_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (referred_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_referral (referrer_id, referred_id)
) ENGINE=InnoDB;
-- جدول المشاريع المؤسسية
CREATE TABLE IF NOT EXISTS organizational_projects (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT NULL,
  start_date DATE NOT NULL,
  end_date DATE NULL,
  status ENUM('planning', 'in_progress', 'on_hold', 'completed', 'cancelled') NOT NULL DEFAULT 'planning',
  priority ENUM('low', 'medium', 'high', 'critical') NOT NULL DEFAULT 'medium',
  budget DECIMAL(20,4) NULL,
  lead_id BIGINT UNSIGNED NOT NULL,
  department VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (lead_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول المهام التنظيمية
CREATE TABLE IF NOT EXISTS organizational_tasks (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  project_id BIGINT UNSIGNED NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT NULL,
  assigned_to BIGINT UNSIGNED NOT NULL,
  created_by BIGINT UNSIGNED NOT NULL,
  status ENUM('todo', 'in_progress', 'review', 'completed', 'blocked') NOT NULL DEFAULT 'todo',
  priority ENUM('low', 'medium', 'high', 'critical') NOT NULL DEFAULT 'medium',
  start_date DATE NULL,
  due_date DATE NULL,
  completion_date DATE NULL,
  estimated_hours DECIMAL(10,2) NULL,
  actual_hours DECIMAL(10,2) NULL,
  FOREIGN KEY (project_id) REFERENCES organizational_projects(id) ON DELETE SET NULL,
  FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول مؤشرات الأداء الرئيسية
CREATE TABLE IF NOT EXISTS key_performance_indicators (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT NULL,
  category ENUM('financial', 'operational', 'customer', 'learning') NOT NULL,
  target_value DECIMAL(20,4) NOT NULL,
  current_value DECIMAL(20,4) NULL,
  unit_of_measurement VARCHAR(50) NOT NULL,
  measurement_frequency ENUM('daily', 'weekly', 'monthly', 'quarterly', 'annually') NOT NULL,
  last_measured_at TIMESTAMP NULL,
  status ENUM('on_track', 'behind', 'at_risk') NOT NULL DEFAULT 'on_track',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- جدول سجل قياس مؤشرات الأداء
CREATE TABLE IF NOT EXISTS kpi_measurements (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kpi_id BIGINT UNSIGNED NOT NULL,
  measured_value DECIMAL(20,4) NOT NULL,
  measurement_date DATE NOT NULL,
  notes TEXT NULL,
  FOREIGN KEY (kpi_id) REFERENCES key_performance_indicators(id) ON DELETE CASCADE,
  UNIQUE KEY unique_kpi_measurement (kpi_id, measurement_date)
) ENGINE=InnoDB;

-- جدول إدارة الموارد البشرية
CREATE TABLE IF NOT EXISTS human_resources_management (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  department VARCHAR(100) NOT NULL,
  job_title VARCHAR(255) NOT NULL,
  employment_type ENUM('full_time', 'part_time', 'contract', 'freelance') NOT NULL,
  hire_date DATE NOT NULL,
  termination_date DATE NULL,
  reporting_to BIGINT UNSIGNED NULL,
  salary DECIMAL(20,4) NOT NULL,
  performance_rating DECIMAL(3,2) NULL,
  skills JSON NULL,
  status ENUM('active', 'suspended', 'terminated', 'on_leave') NOT NULL DEFAULT 'active',
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (reporting_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- جدول التقييم والتطوير الوظيفي
CREATE TABLE IF NOT EXISTS performance_reviews (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  employee_id BIGINT UNSIGNED NOT NULL,
  reviewer_id BIGINT UNSIGNED NOT NULL,
  review_date DATE NOT NULL,
  review_period ENUM('quarterly', 'semi_annual', 'annual') NOT NULL,
  overall_rating DECIMAL(3,2) NOT NULL,
  strengths TEXT NULL,
  areas_for_improvement TEXT NULL,
  development_plan TEXT NULL,
  status ENUM('draft', 'submitted', 'approved', 'completed') NOT NULL DEFAULT 'draft',
  FOREIGN KEY (employee_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول التكاملات التقنية المتقدمة
CREATE TABLE IF NOT EXISTS advanced_technical_integrations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  integration_name VARCHAR(255) NOT NULL,
  integration_type ENUM('api', 'webhook', 'message_queue', 'event_stream', 'data_sync') NOT NULL,
  source_system VARCHAR(100) NOT NULL,
  target_system VARCHAR(100) NOT NULL,
  authentication_method ENUM('oauth2', 'jwt', 'api_key', 'mutual_tls') NOT NULL,
  data_transfer_protocol ENUM('rest', 'graphql', 'grpc', 'websocket') NOT NULL,
  status ENUM('active', 'inactive', 'testing', 'deprecated') NOT NULL DEFAULT 'inactive',
  sync_frequency ENUM('real_time', 'hourly', 'daily', 'weekly') NOT NULL DEFAULT 'daily',
  last_successful_sync TIMESTAMP NULL,
  error_threshold INT UNSIGNED NOT NULL DEFAULT 5,
  current_error_count INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- جدول سجلات التكامل المتقدمة
CREATE TABLE IF NOT EXISTS advanced_integration_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  integration_id BIGINT UNSIGNED NOT NULL,
  direction ENUM('inbound', 'outbound') NOT NULL,
  data_volume BIGINT UNSIGNED NOT NULL,
  processing_time DECIMAL(10,4) NOT NULL,
  status ENUM('success', 'partial', 'failed', 'timeout') NOT NULL,
  error_details TEXT NULL,
  source_endpoint VARCHAR(255) NOT NULL,
  destination_endpoint VARCHAR(255) NOT NULL,
  request_payload LONGTEXT NULL,
  response_payload LONGTEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (integration_id) REFERENCES advanced_technical_integrations(id) ON DELETE CASCADE,
  INDEX idx_integration_status (integration_id, status)
) ENGINE=InnoDB;

-- جدول إدارة الخدمات المتكاملة
CREATE TABLE IF NOT EXISTS integrated_services_catalog (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  service_name VARCHAR(255) NOT NULL,
  service_type ENUM('payment', 'communication', 'logistics', 'banking', 'identity_verification') NOT NULL,
  provider_name VARCHAR(100) NOT NULL,
  api_endpoint VARCHAR(255) NOT NULL,
  authentication_type ENUM('oauth2', 'jwt', 'api_key') NOT NULL,
  supported_operations JSON NOT NULL,
  pricing_model ENUM('per_call', 'monthly_subscription', 'tiered') NOT NULL,
  pricing_details JSON NOT NULL,
  service_reliability DECIMAL(4,2) NOT NULL DEFAULT 99.90,
  average_response_time DECIMAL(10,4) NOT NULL,
  status ENUM('active', 'inactive', 'deprecated') NOT NULL DEFAULT 'inactive',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- جدول متابعة الابتكار والتطوير التقني
CREATE TABLE IF NOT EXISTS technology_innovation_tracking (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  innovation_name VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  category ENUM('ai', 'blockchain', 'cloud', 'security', 'user_experience') NOT NULL,
  research_stage ENUM('conceptual', 'prototype', 'proof_of_concept', 'development', 'production') NOT NULL DEFAULT 'conceptual',
  primary_researcher_id BIGINT UNSIGNED NOT NULL,
  estimated_implementation_date DATE NULL,
  potential_impact_score DECIMAL(4,2) NOT NULL DEFAULT 0.00,
  resource_allocation DECIMAL(20,4) NOT NULL DEFAULT 0.0000,
  status ENUM('active', 'paused', 'completed', 'cancelled') NOT NULL DEFAULT 'active',
  expected_roi DECIMAL(5,2) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (primary_researcher_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول تقييم التقنيات الناشئة
CREATE TABLE IF NOT EXISTS emerging_technology_assessment (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  technology_name VARCHAR(255) NOT NULL,
  assessment_date DATE NOT NULL,
  maturity_level ENUM('emerging', 'developing', 'mature', 'mainstream') NOT NULL,
  potential_applicability JSON NOT NULL,
  risks_identified JSON NOT NULL,
  recommendation ENUM('explore', 'monitor', 'adopt', 'avoid') NOT NULL,
  detailed_report_path VARCHAR(255) NULL,
  assessed_by BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (assessed_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;