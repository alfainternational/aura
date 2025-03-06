import { sleep } from 'k6';
import http from 'k6/http';
import { check, group } from 'k6';
import { Rate, Trend, Counter } from 'k6/metrics';
import { config } from './config.js';

// تعريف المقاييس المخصصة
const getUsersSuccess = new Rate('get_users_success');
const getStatsSuccess = new Rate('get_stats_success');
const getUserDetailSuccess = new Rate('get_user_detail_success');
const getUsersDuration = new Trend('get_users_duration');
const getStatsDuration = new Trend('get_stats_duration');
const getUserDetailDuration = new Trend('get_user_detail_duration');
const failedRequests = new Counter('failed_requests');

// بيانات المسؤول
const adminCredentials = {
  email: 'admin@example.com',
  password: 'AdminPassword123!'
};

// إعداد اختبار الأداء
export const options = {
  scenarios: {
    admin_dashboard_load: {
      executor: 'ramping-vus',
      startVUs: 1,
      stages: [
        { duration: '30s', target: 5 },
        { duration: '1m', target: 5 },
        { duration: '30s', target: 10 },
        { duration: '1m', target: 10 },
        { duration: '30s', target: 1 },
      ],
    },
  },
  thresholds: {
    'get_users_success': ['rate>0.95'],
    'get_stats_success': ['rate>0.95'],
    'get_user_detail_success': ['rate>0.95'],
    'get_users_duration': [`p(95)<${config.thresholds.responseTime.p95}`, `p(99)<${config.thresholds.responseTime.p99}`],
    'get_stats_duration': [`p(95)<${config.thresholds.responseTime.p95}`, `p(99)<${config.thresholds.responseTime.p99}`],
    'get_user_detail_duration': [`p(95)<${config.thresholds.responseTime.p95}`, `p(99)<${config.thresholds.responseTime.p99}`],
    'http_req_duration': [`p(95)<${config.thresholds.responseTime.p95}`, `p(99)<${config.thresholds.responseTime.p99}`],
    'http_req_failed': [`rate<${1 - config.thresholds.successRate}`],
  },
};

export default function() {
  // تسجيل دخول المسؤول
  const loginRes = http.post(`${config.baseUrl}/login`, adminCredentials);
  
  check(loginRes, {
    'admin login successful': (r) => r.status === 200,
    'admin login has token': (r) => JSON.parse(r.body).token !== undefined,
  });
  
  if (loginRes.status !== 200) {
    failedRequests.add(1);
    console.error(`Admin login failed: ${loginRes.status} ${loginRes.body}`);
    return;
  }
  
  const token = JSON.parse(loginRes.body).token;
  const headers = {
    'Authorization': `Bearer ${token}`
  };
  
  group('Admin Dashboard Flow', function() {
    // الحصول على قائمة المستخدمين
    group('Get Users List', function() {
      const startTime = new Date();
      
      const usersRes = http.get(`${config.baseUrl}/admin/users?page=1&per_page=20`, { headers });
      
      const duration = new Date() - startTime;
      getUsersDuration.add(duration);
      
      const success = check(usersRes, {
        'get users status is 200': (r) => r.status === 200,
        'users data is returned': (r) => JSON.parse(r.body).data !== undefined,
      });
      
      getUsersSuccess.add(success);
      
      if (!success) {
        failedRequests.add(1);
      } else {
        // استخراج معرف مستخدم عشوائي للاستخدام لاحقًا
        const usersData = JSON.parse(usersRes.body).data;
        if (usersData && usersData.length > 0) {
          const randomUserIndex = Math.floor(Math.random() * usersData.length);
          const userId = usersData[randomUserIndex].id;
          
          // تخزين معرف المستخدم في متغير عام
          __ENV.randomUserId = userId;
        }
      }
      
      // محاكاة نشاط المستخدم
      sleep(Math.random() * 2 + 1);
    });
    
    // الحصول على الإحصائيات
    group('Get Statistics', function() {
      const startTime = new Date();
      
      const statsRes = http.get(`${config.baseUrl}/admin/statistics`, { headers });
      
      const duration = new Date() - startTime;
      getStatsDuration.add(duration);
      
      const success = check(statsRes, {
        'get stats status is 200': (r) => r.status === 200,
        'stats data is returned': (r) => JSON.parse(r.body) !== undefined,
      });
      
      getStatsSuccess.add(success);
      
      if (!success) {
        failedRequests.add(1);
      }
      
      // محاكاة نشاط المستخدم
      sleep(Math.random() * 2 + 1);
    });
    
    // الحصول على تفاصيل مستخدم
    group('Get User Details', function() {
      // التحقق من وجود معرف مستخدم
      if (__ENV.randomUserId) {
        const startTime = new Date();
        
        const userDetailRes = http.get(`${config.baseUrl}/admin/users/${__ENV.randomUserId}`, { headers });
        
        const duration = new Date() - startTime;
        getUserDetailDuration.add(duration);
        
        const success = check(userDetailRes, {
          'get user detail status is 200': (r) => r.status === 200,
          'user detail is returned': (r) => JSON.parse(r.body) !== undefined,
        });
        
        getUserDetailSuccess.add(success);
        
        if (!success) {
          failedRequests.add(1);
        }
      }
      
      // محاكاة نشاط المستخدم
      sleep(Math.random() * 2 + 1);
    });
    
    // الحصول على إحصائيات المراسلة
    group('Get Messaging Statistics', function() {
      const messagingStatsRes = http.get(`${config.baseUrl}/admin/statistics/messaging`, { headers });
      
      check(messagingStatsRes, {
        'get messaging stats status is 200': (r) => r.status === 200,
        'messaging stats data is returned': (r) => JSON.parse(r.body) !== undefined,
      });
      
      // محاكاة نشاط المستخدم
      sleep(Math.random() * 2 + 1);
    });
    
    // الحصول على إحصائيات المكالمات الصوتية
    group('Get Voice Call Statistics', function() {
      const voiceCallStatsRes = http.get(`${config.baseUrl}/admin/statistics/voice-calls`, { headers });
      
      check(voiceCallStatsRes, {
        'get voice call stats status is 200': (r) => r.status === 200,
        'voice call stats data is returned': (r) => JSON.parse(r.body) !== undefined,
      });
      
      // محاكاة نشاط المستخدم
      sleep(Math.random() * 2 + 1);
    });
    
    // البحث عن مستخدمين
    group('Search Users', function() {
      const searchTerm = 'user';
      
      const searchRes = http.get(`${config.baseUrl}/admin/users/search?q=${searchTerm}`, { headers });
      
      check(searchRes, {
        'search users status is 200': (r) => r.status === 200,
        'search results are returned': (r) => JSON.parse(r.body).data !== undefined,
      });
      
      // محاكاة نشاط المستخدم
      sleep(Math.random() * 2 + 1);
    });
  });
}
