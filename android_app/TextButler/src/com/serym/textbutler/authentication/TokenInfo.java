package com.serym.textbutler.authentication;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;

import org.json.JSONException;
import org.json.JSONObject;

import android.util.Log;

import com.serym.textbutler.Configure;

public class TokenInfo {
	/** The authentication token to use */
	private String authToken;
	/** If we got a error message */
	private boolean errorMessage = false;
	/** The name of the application it is issued too */
	private String issuedTo;
	/** The Google User ID */
	private String userId;
	/** The scope of the token */
	private String scope;
	/** How many seconds left till it expires */
	private int expiresIn;
	/** How long until the token expires */
	private String accessType;

	private static final String ACCESS_URL = "https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=";

	/**
	 * Creates a new token information parser
	 * 
	 * @param authToken
	 *            The authentication token to get information from
	 */
	public TokenInfo(String authToken) {
		this.authToken = authToken;
		reload();
	}

	/**
	 * Get new information from the authentication token
	 */
	public void reload() {
		URL url = null;
		try {
			url = new URL(ACCESS_URL + authToken);
		} catch (MalformedURLException e) {
			Log.e(Configure.TAG, "Invalid URL: " + e);
			return;
		}
		try {
			HttpURLConnection con = (HttpURLConnection) url.openConnection();
			int serverCode = con.getResponseCode();
			// successful query
			if (serverCode == 200) {
				errorMessage = false;
				InputStream is = con.getInputStream();
				parseJson(streamToString(is));
				is.close();
				return;
			} else {
				// did not get a 200, so it's a bad page
				errorMessage = true;
			}
		} catch (IOException e) {
			Log.e(Configure.TAG, "Error Reading JSON: " + e);
		}
	}

	private void parseJson(String json) {
		try {
			JSONObject jsonObj = new JSONObject(json);
			issuedTo = jsonObj.getString("issued_to");
			userId = jsonObj.getString("user_id");
			scope = jsonObj.getString("scope");
			expiresIn = jsonObj.getInt("expires_in");
			accessType = jsonObj.getString("access_type");
		} catch (JSONException e) {
			// This should not occur, since we're parsing google generated
			// json - why would they generate a bad json file?!
			Log.e(Configure.TAG, "Bad JSON File: " + e);
		}

	}

	private String streamToString(InputStream in) throws IOException {
		BufferedReader r = new BufferedReader(new InputStreamReader(in));
		StringBuilder total = new StringBuilder();
		String line;
		while ((line = r.readLine()) != null) {
			total.append(line);
		}
		return total.toString();
	}

	public boolean isErrorMessage() {
		return errorMessage;
	}

	public String getIssuedTo() {
		return issuedTo;
	}

	public String getUserId() {
		return userId;
	}

	public String getScope() {
		return scope;
	}

	public int getExpiresIn() {
		return expiresIn;
	}

	public String getAccessType() {
		return accessType;
	}
	
	
}
