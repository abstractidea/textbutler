package com.serym.textbutler;

import com.google.android.gms.common.AccountPicker;
import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.GooglePlayServicesUtil;
import com.serym.textbutler.authentication.AuthManager;
import com.serym.textbutler.authentication.TokenCallback;
import android.os.Bundle;
import android.accounts.AccountManager;
import android.app.Activity;
import android.content.Intent;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.TextView;
import android.widget.ToggleButton;

public class Configure extends Activity {

	/** Tag used for Logs */
	public static final String TAG = "TextButler";

	/** The Text which lists the current user's name */
	private TextView textCurrentUser;
	/** The button which the user presses to change the current user */
	private Button buttonChangeCurrentUser;
	/** Weather or not the service is on */
	private ToggleButton buttonCurrentlyOn;

	private Button buttonInvlidate;

	/** RequestCodes */
	public static final int REQUEST_NEW_USER = 0x478;

	/** The token for the currently selected user, if applicable */
	private String mainToken;

	/** Provides easy getters and setters for preferences */
	private PreferenceManager pm;
	/** Provides easier access to authentication tokens */
	private AuthManager am;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_configure);

		// check to see if we have the required google play utilities
		int isServiceAvailible = GooglePlayServicesUtil
				.isGooglePlayServicesAvailable(this);
		if (isServiceAvailible != ConnectionResult.SUCCESS) {
			GooglePlayServicesUtil.getErrorDialog(isServiceAvailible, this, 0)
					.show();
			// if the service isn't available, end the application.
			finish();
		}

		// fetch the android ui components
		textCurrentUser = (TextView) findViewById(R.id.currentUser);
		buttonChangeCurrentUser = (Button) findViewById(R.id.changeUser);
		buttonCurrentlyOn = (ToggleButton) findViewById(R.id.statusButton);
		buttonInvlidate = (Button) findViewById(R.id.invalidate);

		// get the managers used to retrieve preferences and authentication
		pm = new PreferenceManager(this);
		am = new AuthManager(pm.getName(), this, new NewToken());

		// update the current name
		updateName();

		// set the onclicklisters for the buttons
		buttonChangeCurrentUser.setOnClickListener(new ChangeUserListener());
		buttonCurrentlyOn.setOnClickListener(new PowerButtonListener());
		buttonInvlidate.setOnClickListener(new InvalidateListener());

		// set the current state for the power button
		buttonCurrentlyOn.setChecked(pm.getPowerState());
	}

	/**
	 * Called when a start activity for result returns.
	 */
	@Override
	public void onActivityResult(int requestCode, int resultCode, Intent intent) {
		// used on return of the account picker dialog
		if (requestCode == REQUEST_NEW_USER && resultCode == Activity.RESULT_OK) {
			// if successful, set the new user.
			String accountName = intent
					.getStringExtra(AccountManager.KEY_ACCOUNT_NAME);
			am.setName(accountName);
			// after we set the account, get authorization for that account.
			am.getToken();
		}

		// used on return from the AuthManager when the user has not yet granted
		// permission.
		if (requestCode == AuthManager.REQUEST_OAUTH_PERMISSION
				&& resultCode == Activity.RESULT_OK) {
			am.getToken();
		}
	}

	/**
	 * Update the current name on the screen
	 * Should be used from a UI Thread.
	 * 
	 */
	private void updateName() {
		String name = pm.getName();
		if (name != null) {
			textCurrentUser.setText(name);
		} else {
			textCurrentUser.setText(R.string.unknown_user);
		}
	}

	/**
	 * Listens for a the user pressing the "Change User" button
	 */
	private class ChangeUserListener implements OnClickListener {
		@Override
		public void onClick(View arg0) {
			// start the account chooser activity
			startActivityForResult(AccountPicker.newChooseAccountIntent(null,
					null, new String[] { "com.google" }, false, null, null,
					null, null), REQUEST_NEW_USER);
		}
	}

	/**
	 * Listens for a user pressing the invalidate button
	 * 
	 * TODO: REMOVE THE INVALIDATE BUTTON
	 */
	private class InvalidateListener implements OnClickListener {

		@Override
		public void onClick(View arg0) {
			Log.d(TAG, "Attempting to invalidate: " + mainToken);
			am.invalidateToken(mainToken);
			pm.setName(null);
			updateName();
		}
	}

	/**
	 * Listens for a user pressing the "on/off" button
	 */

	private class PowerButtonListener implements OnClickListener {
		@Override
		public void onClick(View arg0) {
			pm.setPowerState(buttonCurrentlyOn.isChecked());
		}
	}

	/**
	 * Called when the AuthManager returns a value This does not take place in
	 * the main thread.
	 */
	private class NewToken implements TokenCallback {

		@Override
		public void recieveToken(String name, String token) {
			Log.d(TAG, "Got Token: " + token);
			pm.setName(name);
			mainToken = token;
			// need to call UI updates on the UI Thread
			runOnUiThread(new Runnable() {
				@Override
				public void run() {
					updateName();
				}
			});
		}

		@Override
		public void recieveError(Exception e) {
			Log.e(TAG, "Recieved Exception on Auth:" + e);
			pm.setName(null);
		}

	}
}
