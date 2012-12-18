package com.serym.textbutler.authentication;

import java.io.IOException;

import com.google.android.gms.auth.GoogleAuthException;
import com.google.android.gms.auth.GoogleAuthUtil;
import com.google.android.gms.auth.UserRecoverableAuthException;

import com.serym.textbutler.Configure;

import android.app.Activity;
import android.content.Context;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;

public class AuthManager {

	/** The name to attempt to authenticate with */
	private String name;
	/** The context to authenticate in */
	private Context cntxt;
	/** True if in foreground, false if in background */
	private boolean isForeground;
	/** Callback when not called from an activity */
	private TokenCallback callback;
	/** The minimum time before it's considered expired */
	private static final int MINIMUM_TIME = 60;

	public static final int REQUEST_OAUTH_PERMISSION = 0x479;

	public static final int EB_NUMBER_RETRIES = 5;

	public static final int EB_EXPONENTIAL_NUMBER = 2;

	/**
	 * Create a new AuthManager for authentication. Used to execute
	 * authentication in the background. GoogleAuthUtil will push a notification
	 * on error.
	 * 
	 * @param name
	 *            Name of the user to authenticate.
	 * @param context
	 *            Background context to use.
	 * @param callback
	 *            Callback used on result.
	 */
	public AuthManager(String name, Context context, TokenCallback callback) {
		this.name = name;
		this.cntxt = context;
		this.callback = callback;
		this.isForeground = false;
	}

	/**
	 * Create a new AuthManager for authenticator. Used to execute
	 * authentication in the foreground.
	 * 
	 * If the user has not yet authenticated under this scheme, upon calling
	 * getToken the user will be asked for permission. After permission is
	 * granted, the caller must call getToken again in the methods
	 * onActivityResult. The used requestCode is
	 * AuthManager.REQUEST_OAUTH_PERMISSION
	 * 
	 * If the user has been authenticated on this scheme, the AuthManager will
	 * call recieveToken in callback
	 * 
	 * If an error has occured, the AuthManager will call recieveError in
	 * callback
	 * 
	 * @param name
	 *            The name of the user
	 * @param activity
	 *            The foreground activity to
	 * @param callback
	 *            Callback used on result.
	 */
	public AuthManager(String name, Activity activity, TokenCallback callback) {
		this.name = name;
		this.cntxt = activity;
		this.callback = callback;
		this.isForeground = true;
	}

	/**
	 * Used to change a users name.
	 * 
	 * @param name
	 *            The users name to attempt to get the token for on the next
	 *            query.
	 */
	public void setName(String name) {
		this.name = name;
	}

	public String getToken() {
		// TODO: use onPostExecute

		AsyncTask task = new AsyncTask() {
			@Override
			protected Object doInBackground(Object... act) {
				try {
					String token = "";
					int numRetries = 0;
					boolean success = false;
					IOException lastException = null;
					// execute exponential backoff
					while (numRetries < EB_NUMBER_RETRIES && !success
							&& !Thread.interrupted()) {
						numRetries++;
						try {
							token = getTokenBlocking();
							if (isValid(token)) {
								callback.recieveToken(name, token);
								success = true;
								Log.d(Configure.TAG+"_AM", "Got a valid token");
								return null;
							} else {
								invalidateToken(token);
							}
						} catch (UserRecoverableAuthException e) {
							// methods higher on the call stack have taken care
							// of this.
							Log.d(Configure.TAG+"_AM", "Got a user recoverable auth");
							return null;
						} catch (IOException e) {
							// wait
							try {
								Thread.sleep((long) (Math.pow(
										EB_EXPONENTIAL_NUMBER, numRetries)*1000));
							} catch (InterruptedException interuptedEx) {
								callback.recieveError(e);
								Log.d(Configure.TAG+"_AM", "Interupted Exception");
								return null;
							}
							lastException = e;
						}
					}

					callback.recieveError(lastException != null ? lastException
							: new Exception("Unable to retrieve valid token."));

				} catch (GoogleAuthException e) {
					// according to google's docs, GoogleAuthErrrs shouldn't be
					// retried.
					callback.recieveError(e);
				}
				
				Log.d(Configure.TAG+"_AM", "Returning at end");
				
				return null;

			}
		};
		task.execute();
		return name;
	}

	/**
	 * Should not get called
	 * 
	 * @return
	 */
	public String getTokenBlocking() throws GoogleAuthException, IOException, UserRecoverableAuthException{
		if (isForeground) {
			try {
				String token = GoogleAuthUtil
						.getToken(cntxt, name,
								"oauth2:https://www.googleapis.com/auth/userinfo.profile");
				return token;
			} catch (UserRecoverableAuthException e) {
				((Activity) cntxt).startActivityForResult(e.getIntent(),
						AuthManager.REQUEST_OAUTH_PERMISSION);
				throw e;
			}
		} else {
				String token = GoogleAuthUtil
						.getTokenWithNotification(
								cntxt,
								name,
								"oauth2:https://www.googleapis.com/auth/userinfo.profile",
								new Bundle());
				return token;
			
		}
	}

	public void invalidateToken(String token) {
		GoogleAuthUtil.invalidateToken(cntxt, token);
	}

	public static boolean isValid(String token) {
		if (token == null) {
			return false;
		}
		TokenInfo tokenInfo = new TokenInfo(token);
		Log.d(Configure.TAG, "Time Left: " + tokenInfo.getExpiresIn());
		return !tokenInfo.isErrorMessage()
				&& tokenInfo.getExpiresIn() > MINIMUM_TIME;
	}

}
