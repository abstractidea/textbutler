package com.serym.textbutler.authentication;

public interface TokenCallback {
	public void recieveToken(String name, String token);
	public void recieveError(Exception e);
}
