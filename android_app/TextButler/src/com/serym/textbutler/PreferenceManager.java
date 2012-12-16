package com.serym.textbutler;

import android.app.Activity;
import android.content.Context;
import android.content.SharedPreferences;

public class PreferenceManager {
	
	/** The preference file name */
	public static final String PREFERENCE_FILE = "com.serym.hackathon.textbulter.prefs";
	/** Key to reference the username in the preferences file */
	private static final String USERNAME_KEY = "username";
	/** Key to reference the status of the application in the preferences file */
	private static final String TOGGLE_KEY = "currentlyOn";
	
	private Context parentContext;
	
	public PreferenceManager(Context parent) {
		this.parentContext = parent;
	}
	
	/**
	 * Get the current username
	 * 
	 * @return The current username (or null if none selected)
	 */
	public String getName() {
		SharedPreferences settings = parentContext.getSharedPreferences(PREFERENCE_FILE,
				Context.MODE_PRIVATE);
		return settings.getString(USERNAME_KEY, null);
	}

	/**
	 * Set the current name
	 * @param name The current name
	 */
	public void setName(String name) {
		SharedPreferences settings = parentContext.getSharedPreferences(PREFERENCE_FILE, 0);
		SharedPreferences.Editor editor = settings.edit();
		editor.putString(USERNAME_KEY, name);
		editor.commit();
	}
	
	/**
	 * Get the current power state (whether or not to run)
	 * @return if the app should be running
	 */
	public boolean getPowerState() {
		SharedPreferences settings = parentContext.getSharedPreferences(PREFERENCE_FILE,
				Context.MODE_PRIVATE);
		return settings.getBoolean(TOGGLE_KEY, true);
	}

	/**
	 * Set the current power state
	 * @param power the new power state
	 */
	public void setPowerState(boolean power) {
		SharedPreferences settings = parentContext.getSharedPreferences(PREFERENCE_FILE, 0);
		SharedPreferences.Editor editor = settings.edit();
		editor.putBoolean(TOGGLE_KEY, power);
		editor.commit();
	}
}
