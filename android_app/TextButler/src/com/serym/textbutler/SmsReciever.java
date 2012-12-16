package com.serym.textbutler;

import com.serym.textbutler.authentication.AuthManager;
import com.serym.textbutler.authentication.TokenCallback;
import com.serym.textbutler.authentication.TokenInfo;

import android.content.BroadcastReceiver;
import android.content.ContentResolver;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.telephony.SmsMessage;
import android.util.Log;
import android.widget.Toast;

public class SmsReciever extends BroadcastReceiver {
	/** The name used to the the SMS details from the bundle */
	public static final String SMS_EXTRA_NAME = "pdus";
	public static final String TAG = "TextButlerRvc";

	private Object[] smsMessages;

	@Override
	public void onReceive(Context context, Intent intent) {
		// get preferences and authentication
		PreferenceManager pm = new PreferenceManager(context);
		AuthManager am = new AuthManager(pm.getName(), context,
				new GotAuthInfo());

		Bundle extras = intent.getExtras();
		if (extras != null) {

			ContentResolver contentResolver = context.getContentResolver();
			smsMessages = (Object[]) extras.get(SMS_EXTRA_NAME); //
			am.getToken();
		}
	}

	private class GotAuthInfo implements TokenCallback {

		@Override
		public void recieveToken(String name, String token) {
			// each item in SMS Extra correlates to a text message
			for (int i = 0; i < smsMessages.length; i++) {
				TokenInfo info = new TokenInfo(token);
				SmsMessage sms = SmsMessage.createFromPdu((byte[]) smsMessages[i]);
				String body = sms.getMessageBody().toString();
				String address = sms.getOriginatingAddress();
				
				WebMessage m = new WebMessage(info.getUserId(),token, body,
						address);
				m.send();
			}
		}

		@Override
		public void recieveError(Exception e) {
			Log.e(TAG, "Got error", e);
		}
	}

}
