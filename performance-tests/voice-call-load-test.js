import { sleep } from 'k6';
import http from 'k6/http';
import { check, group } from 'k6';
import { Rate, Trend, Counter } from 'k6/metrics';
import { config } from './config.js';
import { users, login, startVoiceCall } from './helpers.js';

// تعريف المقاييس المخصصة
const startCallSuccess = new Rate('start_call_success');
const acceptCallSuccess = new Rate('accept_call_success');
const endCallSuccess = new Rate('end_call_success');
const startCallDuration = new Trend('start_call_duration');
const acceptCallDuration = new Trend('accept_call_duration');
const endCallDuration = new Trend('end_call_duration');
const failedRequests = new Counter('failed_requests');

// إعداد اختبار الأداء
export const options = {
  scenarios: {
    voice_call_load: {
      executor: 'ramping-vus',
      startVUs: 2,
      stages: [
        { duration: '30s', target: 10 },
        { duration: '1m', target: 10 },
        { duration: '30s', target: 20 },
        { duration: '1m', target: 20 },
        { duration: '30s', target: 2 },
      ],
    },
  },
  thresholds: {
    'start_call_success': ['rate>0.95'],
    'accept_call_success': ['rate>0.95'],
    'end_call_success': ['rate>0.95'],
    'start_call_duration': [`p(95)<${config.thresholds.responseTime.p95}`, `p(99)<${config.thresholds.responseTime.p99}`],
    'accept_call_duration': [`p(95)<${config.thresholds.responseTime.p95}`, `p(99)<${config.thresholds.responseTime.p99}`],
    'end_call_duration': [`p(95)<${config.thresholds.responseTime.p95}`, `p(99)<${config.thresholds.responseTime.p99}`],
    'http_req_duration': [`p(95)<${config.thresholds.responseTime.p95}`, `p(99)<${config.thresholds.responseTime.p99}`],
    'http_req_failed': [`rate<${1 - config.thresholds.successRate}`],
  },
};

export default function() {
  // اختيار مستخدمين عشوائيين
  const callerIndex = Math.floor(Math.random() * users.length);
  let receiverIndex;
  do {
    receiverIndex = Math.floor(Math.random() * users.length);
  } while (receiverIndex === callerIndex);
  
  const caller = users[callerIndex];
  const receiver = users[receiverIndex];
  
  // تسجيل دخول المتصل
  const callerToken = login(caller.email, caller.password);
  
  if (!callerToken) {
    failedRequests.add(1);
    return;
  }
  
  // تسجيل دخول المستقبل
  const receiverToken = login(receiver.email, receiver.password);
  
  if (!receiverToken) {
    failedRequests.add(1);
    return;
  }
  
  group('Voice Call Flow', function() {
    // بدء مكالمة صوتية
    let callId;
    
    group('Start Voice Call', function() {
      const startTime = new Date();
      
      callId = startVoiceCall(callerToken, [receiver.email]);
      
      const duration = new Date() - startTime;
      startCallDuration.add(duration);
      
      const success = callId !== null;
      startCallSuccess.add(success);
      
      if (!success) {
        failedRequests.add(1);
        return;
      }
      
      // محاكاة نشاط المستخدم
      sleep(Math.random() * 2 + 1);
    });
    
    if (callId) {
      // قبول المكالمة
      group('Accept Voice Call', function() {
        const headers = {
          'Authorization': `Bearer ${receiverToken}`,
          'Content-Type': 'application/json'
        };
        
        const startTime = new Date();
        
        const acceptRes = http.put(`${config.baseUrl}/voice-calls/${callId}/accept`, {}, { headers });
        
        const duration = new Date() - startTime;
        acceptCallDuration.add(duration);
        
        const success = check(acceptRes, {
          'accept call status is 200': (r) => r.status === 200,
        });
        
        acceptCallSuccess.add(success);
        
        if (!success) {
          failedRequests.add(1);
        }
        
        // محاكاة مدة المكالمة
        sleep(Math.random() * 3 + 2);
      });
      
      // إنهاء المكالمة
      group('End Voice Call', function() {
        const headers = {
          'Authorization': `Bearer ${callerToken}`,
          'Content-Type': 'application/json'
        };
        
        const startTime = new Date();
        
        const endRes = http.put(`${config.baseUrl}/voice-calls/${callId}/end`, {}, { headers });
        
        const duration = new Date() - startTime;
        endCallDuration.add(duration);
        
        const success = check(endRes, {
          'end call status is 200': (r) => r.status === 200,
        });
        
        endCallSuccess.add(success);
        
        if (!success) {
          failedRequests.add(1);
        }
        
        // محاكاة نشاط المستخدم
        sleep(Math.random() * 2 + 1);
      });
    }
  });
}
