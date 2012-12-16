package com.serym.textbutler;

import java.net.MalformedURLException;
import java.net.URL;

import org.json.JSONException;
import org.json.JSONObject;

import android.os.AsyncTask;
import android.util.Log;

/**
 * Wrapper intended to send a message/notification to the TextButler web
 * application.
 * 
 * this.userGoogleID this.authToken this.smsMessage this.smsSender
 * 
 * @author Matthew Vaughn
 * @since 2012/12/15
 */
public class WebMessage {
	/**
	 * Address for resource of web application that will handles messages.
	 */
	private static final String WEB_APPLICATION_MESSAGE_HANDLER = "http://hackathon.serym.com/?gen_message";

	/**
	 * Message parameter key for Google ID of the user of the android
	 * application.
	 */
	private static final String MESSAGE_PARAM_USER_GOOGLE_ID = "user_google_id";

	/**
	 * Message parameter key for auth token that has been received through
	 * Google's OAuth framework.
	 */
	private static final String MESSAGE_PARAM_AUTH_TOKEN = "auth_token";

	/**
	 * Message parameter key for text message of an SMS received on user's
	 * android device.
	 */
	private static final String MESSAGE_PARAM_SMS_MESSAGE = "sms_message";

	/**
	 * Message parameter key for name or phone number of the sender of an SMS
	 * received on user's android device.
	 */
	private static final String MESSAGE_PARAM_SMS_SENDER = "sms_sender";

	/**
	 * Data for web message that is intended to be communicated to the web
	 * application. Params include those for: user Google ID, auth token, SMS
	 * message, SMS sender.
	 */
	private JSONObject mMessage;

	/**
	 * Logcat tag.
	 */
	private static final String LOG_TAG = "WebMessage";

	/**
	 * Constructor for a web message that is intended to be sent to the web
	 * application.
	 * 
	 * @param userGoogleID
	 *            Google ID of the user of the android application
	 * @param authToken
	 *            Auth token that has been received through Google's OAuth
	 *            framework
	 * @param smsMessage
	 *            Text message of an SMS received on user's android device
	 * @param smsSender
	 *            Name or phone number of the sender of an SMS received on
	 *            user's android device
	 */
	public WebMessage(String userGoogleID, String authToken, String smsMessage,
			String smsSender) {
		this.mMessage = new JSONObject();
		try {
			this.mMessage.put(MESSAGE_PARAM_USER_GOOGLE_ID, userGoogleID);
			this.mMessage.put(MESSAGE_PARAM_AUTH_TOKEN, authToken);
			this.mMessage.put(MESSAGE_PARAM_SMS_MESSAGE, smsMessage);
			this.mMessage.put(MESSAGE_PARAM_SMS_SENDER, smsSender);
		} catch (JSONException e) {
			// TODO properly handle exception
			e.printStackTrace();
			Log.d(LOG_TAG, "message JSON failed to be formed");
		}
	}

	/**
	 * Sends message to web application, formatted as JSON. Message data
	 * includes: user Google ID, auth token, sms message, sms sender.
	 * 
	 * @requires The the OAuth token indicated by this this.authToken must still
	 *           be valid.
	 * @throws ServerRequestException
	 */
	public void send() {
		Log.d(LOG_TAG, "send()");
		AsyncTask<Object, Object, Object> task = new AsyncTask<Object, Object, Object>() {
			@Override
			protected Object doInBackground(Object... params) {
				try {
					URL webAppMessageHandler = new URL(
							WEB_APPLICATION_MESSAGE_HANDLER);

					Log.d(LOG_TAG, "webAppMessageHandler = "
							+ webAppMessageHandler);
					Log.d(LOG_TAG, "web message:  " + mMessage.toString());
					ServerRequest.send(webAppMessageHandler,
							mMessage.toString());
				} catch (MalformedURLException e) {
					// TODO properly handle exception
					Log.d(LOG_TAG,
							"URL for server request (intended for web application) failed to be sent",e);
				} catch (ServerRequestException e) {
					// TODO Auto-generated catch block
					Log.d(LOG_TAG, "Error on Send",e);
				}
				return null;
			}
		};
		task.execute();

	}
}
