import { sleep } from 'k6';
import http from 'k6/http';
import { check, group } from 'k6';
import { Counter, Rate, Trend } from 'k6/metrics';
import { config } from './config.js';
import { users } from './helpers.js';

// تعريف المقاييس المخصصة
const loginSuccess = new Rate('login_success');
const registerSuccess = new Rate('register_success');
const loginDuration = new Trend('login_duration');
const registerDuration = new Trend('register_duration');
const failedRequests = new Counter('failed_requests');

// إعداد اختبار الأداء
export const options = {
  scenarios: {
    login_load: {
      executor: 'ramping-arrival-rate',
      startRate: config.users.arrivalRate.low,
      timeUnit: '1s',
      preAllocatedVUs: 50,
      maxVUs: 100,
      stages: [
        { duration: '30s', target: config.users.arrivalRate.medium },
        { duration: '1m', target: config.users.arrivalRate.medium },
        { duration: '30s', target: config.users.arrivalRate.high },
        { duration: '1m', target: config.users.arrivalRate.high },
        { duration: '30s', target: config.users.arrivalRate.low },
      ],
    },
  },
  thresholds: {
    'login_success': ['rate>0.95'],
    'register_success': ['rate>0.95'],
    'login_duration': [`p(95)<${config.thresholds.responseTime.p95}`, `p(99)<${config.thresholds.responseTime.p99}`],
    'register_duration': [`p(95)<${config.thresholds.responseTime.p95}`, `p(99)<${config.thresholds.responseTime.p99}`],
    'http_req_duration': [`p(95)<${config.thresholds.responseTime.p95}`, `p(99)<${config.thresholds.responseTime.p99}`],
    'http_req_failed': [`rate<${1 - config.thresholds.successRate}`],
  },
};

export default function() {
  const userIndex = Math.floor(Math.random() * users.length);
  const user = users[userIndex];
  
  group('Authentication Flow', function() {
    // تسجيل الدخول
    group('Login', function() {
      const startTime = new Date();
      
      const loginRes = http.post(`${config.baseUrl}/login`, {
        email: user.email,
        password: user.password
      });
      
      const duration = new Date() - startTime;
      loginDuration.add(duration);
      
      const success = check(loginRes, {
        'login status is 200': (r) => r.status === 200,
        'login has token': (r) => JSON.parse(r.body).token !== undefined,
      });
      
      loginSuccess.add(success);
      
      if (!success) {
        failedRequests.add(1);
        console.error(`Login failed for ${user.email}: ${loginRes.status} ${loginRes.body}`);
      }
      
      // محاكاة نشاط المستخدم
      sleep(Math.random() * 3 + 1);
    });
    
    // تسجيل مستخدم جديد (مع اسم مستخدم عشوائي لتجنب التكرار)
    group('Register', function() {
      const randomSuffix = Math.floor(Math.random() * 1000000);
      const newUser = {
        name: `New User ${randomSuffix}`,
        email: `newuser${randomSuffix}@example.com`,
        password: 'Password123!',
        password_confirmation: 'Password123!',
        country: 'Sudan',
        city: 'Khartoum'
      };
      
      const startTime = new Date();
      
      const registerRes = http.post(`${config.baseUrl}/register`, newUser);
      
      const duration = new Date() - startTime;
      registerDuration.add(duration);
      
      const success = check(registerRes, {
        'register status is 201': (r) => r.status === 201,
        'register has user data': (r) => JSON.parse(r.body).user !== undefined,
      });
      
      registerSuccess.add(success);
      
      if (!success) {
        failedRequests.add(1);
        console.error(`Register failed for ${newUser.email}: ${registerRes.status} ${registerRes.body}`);
      }
      
      // محاكاة نشاط المستخدم
      sleep(Math.random() * 3 + 1);
    });
    
    // تسجيل الخروج
    group('Logout', function() {
      // أولاً، تسجيل الدخول للحصول على رمز
      const loginRes = http.post(`${config.baseUrl}/login`, {
        email: user.email,
        password: user.password
      });
      
      if (loginRes.status === 200) {
        const token = JSON.parse(loginRes.body).token;
        
        const logoutRes = http.post(`${config.baseUrl}/logout`, {}, {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
        
        check(logoutRes, {
          'logout status is 200': (r) => r.status === 200,
        });
      }
      
      // محاكاة نشاط المستخدم
      sleep(Math.random() * 2 + 1);
    });
  });
}
