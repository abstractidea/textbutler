package com.serym.textbutler;


import android.os.Bundle;
import android.app.Activity;
import android.util.Log;

public class MainActivity extends Activity {
	private static final String LOG_TAG = "TextButler.MainActivity";

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);

		WebMessage m = new WebMessage("Yoshi", "abc123", "how's it going?",
				"Boshi");
		try {
			m.send();
		} catch (ServerRequestException e) {
			// TODO properly handle exception
			Log.d(LOG_TAG, "message failed to be sent to web application");
			e.printStackTrace();
		}

		Log.d(LOG_TAG, "app created");
	}

}
